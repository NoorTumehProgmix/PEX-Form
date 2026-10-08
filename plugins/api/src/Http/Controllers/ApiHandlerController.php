<?php

namespace Progmix\Api\Http\Controllers;

use Progmix\Api\Models\Api;
use Progmix\Api\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Juzaweb\CMS\Http\Controllers\BackendController;

class ApiHandlerController extends BackendController
{
    public function index($any, Request $request)
    {
        $api = Api::find($request->api_id);

        if ($api->maintenance == 1) { //Maintenance Mode is ON
            $response_code = 503;
            $response_message = ['status' => false, 'code' => $response_code, 'response' => $api->message];
            return response()->json($response_message, $response_code);
        }

        $merged_params = $request->merged_params;
        $attemptId = $request->attempt_id;

        $origin_url = $api->origin_url;
        $params = json_decode($api->params);
        if ($params != null) {
            foreach ($params as $param => $value) {
                if ($value) {
                    $origin_url = str_replace('{' . $param . '}', $value, $origin_url);
                } else {
                    $origin_url = str_replace('{' . $param . '}',  $merged_params[$param], $origin_url);
                }
            }
        }

        $headers = json_decode($api->headers, true);
        $body = json_decode($api->body, true);
        $method = strtolower($api->method);

        $startTime = microtime(true);
        $o_apiLog = new ApiLog();
        $o_apiLog->start = now()->format('Y-m-d H:i:s');
        $o_apiLog->api_id = $api->id;
        $o_apiLog->attempt_id = $attemptId;
        $o_apiLog->request = json_encode($api->origin_url);
        $o_apiLog->response = null;
        $o_apiLog->type = 'edge/origin';
        $o_apiLog->ip = $request->ip();
        $o_apiLog->status_code = null;
        $o_apiLog->duration = null;
        $o_apiLog->end = null;
        $o_apiLog->save();

        try {

            $timeout = isset($api->timeout) ? $api->timeout : 0;
            if ($method == 'get') {
                $response = Http::timeout($timeout)->$method($origin_url);
            } else {
                $response = Http::timeout($timeout)->withHeaders($headers)->$method($origin_url, $body);
            }
            $o_apiLog->status_code = $response->status();
            $response_code = $o_apiLog->status_code;

            if (floor($o_apiLog->status_code / 100) == 2) {

                $responseData = json_decode($response->body(), true);
                if (isset($api->response_pairs) && $api->response_pairs != "") {
                    foreach (json_decode($api->response_pairs) as $pair) {
                        if (array_key_exists($pair->key, $responseData) && $responseData[$pair->key] == $pair->value) {
                            $responseData[$pair->key] = $pair->friendly_value;
                        }
                    }
                }

                $response_message = ['status' => true, 'code' => $response_code, 'response' => $responseData];

                $o_apiLog->response =  $response->body();
            } else {
                $response_message = ['status' => false, 'code' => $response_code, 'response' => $api->message];
                $o_apiLog->response =  $response_message;
            }
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Operation timed out') !== false) {
                // Handle timeout exception
                $response_code = 408;
                $response_message = [
                    'status' => false,
                    'code' => $response_code,
                    'message' => $api->timeout_message,
                ];
            } else {
                // Handle request exceptions
                $response_code = 500;
                $response_message = [
                    'status' => false,
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ];
            }
            $o_apiLog->response = json_encode([]);
            $o_apiLog->status_code = $response_code;
        }
        $endTime = microtime(true);
        $duration = number_format($endTime - $startTime, 3);

        $o_apiLog->end =  now()->format('Y-m-d H:i:s');
        $o_apiLog->duration = $duration;
        $o_apiLog->save();

        return response()->json($response_message, $response_code);
    }
}
