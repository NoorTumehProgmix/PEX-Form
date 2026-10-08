<?php

namespace Progmix\Api\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Progmix\Api\Models\Api;
use Progmix\Api\Models\ApiLog;

class CalculateRequestDuration
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $segments = explode('/', $request->path());
        $api = Api::where('version', $segments[1])
            ->where('slug', $segments[2])
            ->where('status', 1)
            ->firstOrFail();

        $edge_segments = explode('/', $api->edge_url);

        array_splice($segments, 0, 3);
        array_splice($edge_segments, 0, 4);

        if (count($segments) != count($edge_segments)) {
            abort(404, 'params failed');
        }

        $cleaned_edge_segments = array_map(function ($value) {
            return str_replace(['{', '}'], '', $value);
        }, $edge_segments);

        $merged_params = array_combine($cleaned_edge_segments, $segments);

        $attemptId = (ApiLog::latest()?->first()?->attempt_id + 1) ?? 1;

        $request->merge(['attempt_id' => $attemptId, 'merged_params' => $merged_params, 'api_id' => $api->id]);

        $startTime = microtime(true);
        $apiLog = new ApiLog();
        $apiLog->attempt_id = $attemptId;
        $apiLog->start = now()->format('Y-m-d H:i:s');
        $apiLog->api_id =  $api->id;
        $apiLog->request = json_encode($api->edge_url);
        $apiLog->response = null;
        $apiLog->type = 'client/edge';
        $apiLog->ip = $request->ip();
        $apiLog->status_code = null;
        $apiLog->duration = null;
        $apiLog->end = null;
        $apiLog->save();


        $response = $next($request);

        $apiLog->status_code = $response->getStatusCode();
        $apiLog->response =  $response->getContent();

        $apiLog->end = now()->format('Y-m-d H:i:s');
        $endTime = microtime(true);
        $durationMillis = ($endTime - $startTime) * 1000;
        $apiLog->duration = $durationMillis;
        $apiLog->save();

        return $response;
    }
}
