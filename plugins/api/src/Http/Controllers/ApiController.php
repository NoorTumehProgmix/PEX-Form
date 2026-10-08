<?php

namespace Progmix\Api\Http\Controllers;

use Progmix\Api\Http\Datatables\ApisDatatable;
use Progmix\Api\Http\Requests\ApiRequest;
use Progmix\Api\Models\Api;
use Illuminate\Http\Request;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Juzaweb\Backend\Models\Language;
use Illuminate\Support\Str;

use Juzaweb\CMS\Traits\ResourceController;

class ApiController extends BackendController
{
    use ResourceController {
        getDataForForm as DataForForm;
        afterSave as tAfterSave;
    }

    protected $viewPrefix = 'api::api';

    protected function getDataTable(...$params)
    {
        return new ApisDatatable();
    }

    protected function validator(array $attributes, ...$params)
    {
        $validator = Validator::make($attributes, []);

        return $validator;
    }

    protected function getModel(...$params)
    {
        return Api::class;
    }

    protected function getTitle(...$params)
    {
        return 'API';
    }
    protected function getDataForForm($model, ...$params): array
    {
        $data = $this->DataForForm($model);
        return $data;
    }


    protected function create(...$params): View
    {

        $methods = array('GET' => 'GET', 'POST' => 'POST', 'PUT' => 'PUT', 'DELETE' => 'DELETE', 'PATCH' => 'PATCH');
        $status = array('1' => trans_cms('cms::app.active'), '0' => trans_cms('cms::app.inactive'));
        $maintenance_modes = array('0' => trans_cms('cms::app.off'), '1' => trans_cms('cms::app.on'));

        $this->checkPermission(
            'index',
            $this->getModel(...$params),
            ...$params
        );

        if (method_exists($this, 'getBreadcrumbPrefix')) {
            $this->getBreadcrumbPrefix(...$params);
        }

        return view(
            "{$this->viewPrefix}.add",
            array_merge(
                [
                    'methods' => $methods,
                    'status' => $status,
                    'maintenance_modes' => $maintenance_modes,
                ],
                $this->getDataForIndex(...$params)
            )
        );
    }

    protected function store(ApiRequest $request)
    {
        $data = $request->validated();

        $data['slug']  = str($data['edge_name'])->slug();

        $edgeUrl = '/api' . '/' . $data['version'] . '/' .   $data['slug'];
        $data['headers'] = [];
        $data['params']  = [];
        $data['query'] = [];
        $data['body'] = [];
        $data['response_pairs'] = [];

        if ($request->header_keys) {
            foreach ($request->header_keys as $index => $key) {
                $data['headers'][$key] = $request->header_values[$index];
            }
        }

        foreach ($request->response_keys as $index => $key) {
            $value = $request->response_values[$index];
            $friendlyValue = $request->response_friendly[$index];
            $data['response_pairs'][] = [
                'key' => $key,
                'value' => $value,
                'friendly_value' => $friendlyValue,
            ];
        }

        if ($request->param_keys) {
            foreach ($request->param_keys as $index => $key) {
                $data['params'][$key] = $request->param_values[$index];
            }
            $edgeParams = array_merge($data['params']);
            $nullParams = array_filter($edgeParams, function ($value) {
                return $value === null;
            });
            if (!empty($nullParams)) {
                $paramString = implode('/', array_map(function ($param) {
                    return '{' . $param . '}';
                }, array_keys($nullParams)));

                $edgeUrl .= '/' . $paramString;
            }
        }
        if ($request->query_keys) {
            foreach ($request->query_keys as $index => $key) {
                $data['query'][$key] = $request->query_values[$index];
            }
        }

        if ($request->body_keys) {
            foreach ($request->body_keys as $index => $key) {
                $data['body'][$key] = $request->body_values[$index];
            }
        }
        unset($data['header_keys']);
        unset($data['header_values']);
        unset($data['resoonse_keys']);
        unset($data['response_values']);
        unset($data['query_keys']);
        unset($data['query_values']);
        unset($data['body_keys']);
        unset($data['body_values']);
        unset($data['body']['']);
        unset($data['headers']['']);
        unset($data['query']['']);
        $data['edge_url'] = $edgeUrl;
        $data['body'] = json_encode($data['body']);
        $data['headers'] = json_encode($data['headers']);
        $data['response_pairs'] = json_encode($data['response_pairs']);
        $data['params'] = json_encode($data['params']);
        $data['query'] = json_encode($data['query']);

        Api::create($data);
        return redirect()->route('api.index');
    }


    public function edit($api, ...$params): View
    {
        $methods = array('GET' => 'GET', 'POST' => 'POST', 'PUT' => 'PUT', 'DELETE' => 'DELETE', 'PATCH' => 'PATCH');
        $status = array('1' => trans_cms('cms::app.active'), '0' => trans_cms('cms::app.inactive'));
        $maintenance_modes = array('0' => trans_cms('cms::app.off'), '1' => trans_cms('cms::app.on'));

        $api = Api::where('id', $api)
            ->firstOrFail();

        return view(
            "{$this->viewPrefix}.edit",
            array_merge(
                [
                    'api' => $api,
                    'methods' => $methods,
                    'status' => $status,
                    'maintenance_modes' => $maintenance_modes
                ],
                $this->getDataForIndex(...$params)
            )
        );
    }


    public function update(ApiRequest $request, Api $api)
    {
        $data = $request->validated();
        $data['slug']  = str($data['edge_name'])->slug();
        $edgeUrl = '/api' . '/' . $data['version'] . '/' . $data['slug'];
        $data['headers'] = [];
        $data['params'] = [];
        $data['query'] = [];
        $data['body'] = [];
        $data['response_pairs'] = [];

        if ($request->header_keys) {
            foreach ($request->header_keys as $index => $key) {
                $data['headers'][$key] = $request->header_values[$index];
            }
        }

        foreach ($request->response_keys as $index => $key) {
            $value = $request->response_values[$index];
            $friendlyValue = $request->response_friendly[$index];
            $data['response_pairs'][] = [
                'key' => $key,
                'value' => $value,
                'friendly_value' => $friendlyValue,
            ];
        }

        if ($request->param_keys) {
            foreach ($request->param_keys as $index => $key) {
                $data['params'][$key] = $request->param_values[$index];
            }
            $edgeParams = array_merge($data['params']);
            $nullParams = array_filter($edgeParams, function ($value) {
                return $value === null;
            });
            if (!empty($nullParams)) {
                $paramString = implode('/', array_map(function ($param) {
                    return '{' . $param . '}';
                }, array_keys($nullParams)));

                $edgeUrl .= '/' . $paramString;
            }
        }
        if ($request->query_keys) {
            foreach ($request->query_keys as $index => $key) {
                $data['query'][$key] = $request->query_values[$index];
            }
        }

        if ($request->body_keys) {
            foreach ($request->body_keys as $index => $key) {
                $data['body'][$key] = $request->body_values[$index];
            }
        }
        unset($data['header_keys']);
        unset($data['header_values']);
        unset($data['response_keys']);
        unset($data['response_values']);
        unset($data['query_keys']);
        unset($data['query_values']);
        unset($data['body_keys']);
        unset($data['body_values']);
        unset($data['body']['']);
        unset($data['headers']['']);
        unset($data['response_pairs']['']);
        unset($data['query']['']);
        $data['edge_url'] = $edgeUrl;
        $data['body'] = json_encode($data['body']);
        $data['headers'] = json_encode($data['headers']);
        $data['response_pairs'] = json_encode($data['response_pairs']);
        $data['params'] = json_encode($data['params']);
        $data['query'] = json_encode($data['query']);

        $api->update($data);
        $this->success(
            [
                'message' => trans_cms('cms::app.saved_successfully'),
            ]
        );
        return redirect()->route('api.edit', ['api' => $api]);
    }
}
