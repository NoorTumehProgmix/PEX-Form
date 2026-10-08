@extends('cms::layouts.backend')

@section('header')
    {{ Vite::useBuildDirectory('plugins/api') }}
    @vite('plugins/api/src/resources/assets/app.css')
    @vite('plugins/api/src/resources/assets/app.js')
@endsection

@section('content')

    <form action="{{ route('api.create') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-6"></div>
            <div class="col-md-6 text-right">
                <div class="btn-group">
                    <button type="submit" class="btn btn-success px-5">
                        <i class="fa fa-save"></i> {{ trans_cms('cms::app.save') }}
                    </button>

                    <button type="button" class="btn btn-warning cancel-button px-3">
                        <i class="fa fa-refresh"></i> {{ trans_cms('cms::app.reset') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <div class="row mb-2">
                    <div class="col-md-6">
                        {{ Field::text(trans_cms('api::content.api_name'), 'name', [
                            'value' => old('name'),
                        ]) }}
                        @error('name')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        {{ Field::text(trans_cms('cms::app.description'), 'description', [
                            'value' => old('message'),
                        ]) }}
                        @error('description')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        {{ Field::text(trans_cms('api::content.edge_name'), 'edge_name', [
                            'value' => old('edge_name'),
                        ]) }}
                        @error('edge_name')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        {{ Field::text(trans_cms('api::content.edge_version'), 'version', [
                            'value' => old('version'),
                        ]) }}
                        @error('version')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>



                <div class="row mb-2">
                    <div class="col-md-6">
                        {{ Field::text(trans_cms('api::content.origin_url'), 'origin_url', [
                            'value' => old('origin_url'),
                        ]) }}
                        @error('origin_url')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        {{ Field::select(trans_cms('api::content.method'), 'method', [
                            'options' => $methods,
                            'value' => old('method'),
                        ]) }}
                        @error('method')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        {{ Field::select(trans_cms('api::content.maintenance'), 'maintenance', [
                            'options' => $maintenance_modes,
                            'value' => old('maintenance'),
                        ]) }}
                        @error('maintenance')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        {{ Field::text(trans_cms('api::content.return_message'), 'message', [
                            'value' => old('message'),
                        ]) }}
                        @error('message')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6">
                        {{ Field::text(trans_cms('api::content.timeout'), 'timeout', [
                            'value' => old('timeout'),
                        ]) }}
                        @error('timeout')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        {{ Field::text(trans_cms('api::content.timeout_message'), 'timeout_message', [
                            'value' => old('timeout_message'),
                        ]) }}
                        @error('timeout_message')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="row mb-2">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ trans_cms('api::content.response_keys') }}</label>
                            <div class="keys-repeater-container">
                                @if (old('response_keys'))
                                    @foreach (old('response_keys') as $index => $key)
                                        <div class="row mb-2 repeater-item">
                                            <div class="col-md-4"><input class="form-control" name="response_keys[]"
                                                    type="text" placeholder="Key"
                                                    value="{{ old('response_keys')[$index] }}">
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-control" name="response_values[]" type="text"
                                                    placeholder="Value" value="{{ old('response_values')[$index] }}">
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-control" name="response_friendly[]" type="text"
                                                    placeholder="Friendly value"
                                                    value="{{ old('response_friendly')[$index] }}">
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row mb-2 repeater-item">
                                        <div class="col-md-4">
                                            <input class="form-control" name="response_keys[]" type="text"
                                                placeholder="Key">
                                        </div>
                                        <div class="col-md-4">
                                            <input class="form-control" name="response_values[]" type="text"
                                                placeholder="Value">
                                        </div>
                                        <div class="col-md-4">
                                            <input class="form-control" name="response_friendly[]" type="text"
                                                placeholder="Friendly Value">
                                        </div>
                                    </div>
                                @endif
                                @error('response_keys.*')
                                    <div class="error">{{ $message }}</div>
                                @enderror
                                @error('response_values.*')
                                    <div class="error">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="button" class="btn btn-primary add-reponse-row" data-attr="keys">Add
                                Key/Value</button>

                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ trans_cms('api::content.headers') }}</label>
                            <div class="header-repeater-container">
                                @if (old('header_keys'))
                                    @foreach (old('header_keys') as $index => $key)
                                        <div class="row mb-2 repeater-item">
                                            <div class="col-md-6"><input class="form-control" name="header_keys[]"
                                                    type="text" placeholder="Key"
                                                    value="{{ old('header_keys')[$index] }}">
                                            </div>
                                            <div class="col-md-6">
                                                <input class="form-control" name="header_values[]" type="text"
                                                    placeholder="Value" value="{{ old('header_values')[$index] }}">
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row mb-2 repeater-item">
                                        <div class="col-md-6">
                                            <input class="form-control" name="header_keys[]" type="text"
                                                placeholder="Key">
                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control" name="header_values[]" type="text"
                                                placeholder="Value">
                                        </div>
                                    </div>
                                @endif
                                @error('header_keys.*')
                                    <div class="error">{{ $message }}</div>
                                @enderror
                                @error('header_values.*')
                                    <div class="error">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="button" class="btn btn-primary add-key-value" data-attr="header">Add
                                Key/Value</button>

                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ trans_cms('api::content.params') }}</label>
                            <div class="param-repeater-container">
                                @if (old('param_keys'))
                                    @foreach (old('param_keys') as $index => $key)
                                        <div class="row mb-2 repeater-item">
                                            <div class="col-md-6">
                                                <input class="form-control" name="param_keys[]" type="text"
                                                    placeholder="Key" value="{{ old('param_keys')[$index] }}">
                                            </div>
                                            <div class="col-md-6">

                                                <input class="form-control" name="param_values[]" type="text"
                                                    placeholder="Value" value="{{ old('param_values')[$index] }}">
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group repeater-item">
                                        <p>Fill the params in the the origin url by using /{param_name}/ Or
                                            /create_number_{param_name}/
                                            in the url</p>
                                    </div>
                                @endif
                                @error('param_keys.*')
                                    <div class="error">{{ $message }}</div>
                                @enderror
                                @error('param_values.*')
                                    <div class="error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ trans_cms('api::content.query') }}</label>
                            <div class="query-repeater-container">
                                @if (old('query_keys'))
                                    @foreach (old('query_keys') as $index => $key)
                                        <div class="row mb-2 repeater-item">
                                            <div class="col-md-6">
                                                <input class="form-control" name="query_keys[]" type="text"
                                                    placeholder="Key" value="{{ old('query_keys')[$index] }}">
                                            </div>
                                            <div class="col-md-6">
                                                <input class="form-control" name="query_values[]" type="text"
                                                    placeholder="Value" value="{{ old('query_values')[$index] }}">
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row mb-2 repeater-item">
                                        <div class="col-md-6">
                                            <input class="form-control" name="query_keys[]" type="text"
                                                placeholder="Key">
                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control" name="query_values[]" type="text"
                                                placeholder="Value">
                                        </div>
                                    </div>
                                @endif
                                @error('query_keys.*')
                                    <div class="error">{{ $message }}</div>
                                @enderror
                                @error('query_values.*')
                                    <div class="error">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="button" class="btn btn-primary add-key-value" data-attr="query">Add
                                Key/Value</button>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="row_custom bm-3">
                                <label>{{ trans_cms('api::content.request_body') }}</label>
                                <div class="body-repeater-container">
                                    @if (old('body_keys'))
                                        @foreach (old('body_keys') as $index => $key)
                                            <div class="row mb-2 repeater-item">
                                                <div class="col-md-6">
                                                    <input class="form-control" name="body_keys[]" type="text"
                                                        placeholder="Key" value="{{ old('body_keys')[$index] }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <input class="form-control" name="body_values[]" type="text"
                                                        placeholder="Value" value="{{ old('body_values')[$index] }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="row mb-2 repeater-item">
                                            <div class="col-md-6">
                                                <input class="form-control" name="body_keys[]" type="text"
                                                    placeholder="Key">
                                            </div>
                                            <div class="col-md-6">
                                                <input class="form-control" name="body_values[]" type="text"
                                                    placeholder="Value">
                                            </div>
                                        </div>
                                    @endif
                                    @error('body_keys.*')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                    @error('body_values.*')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="button" class="btn btn-primary add-key-value" data-attr="body">Add
                                    Key/Value</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                {{ Field::select(trans_cms('cms::app.status'), 'status', [
                    'options' => $status,
                    'value' => old('status'),
                ]) }}
                @error('status')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </form>

    <template id="repeater-template">
        <div class="row mb-2 repeater-item">
            <div class="col-md-6">
                <input class="form-control" type="text" name="propName_keys[]" placeholder="Key">
            </div>
            <div class="col-md-6">
                <input class="form-control" type="text" name="propName_values[]" placeholder="Value">
            </div>
            <div class="col-md-2 del-col">
                <button type="button" class="btn btn-danger remove-item">Remove</button>
            </div>

        </div>
    </template>

    <template id="response-repeater-template">
        <div class="row mb-2 repeater-item">
            <div class="col-md-4">
                <input class="form-control" type="text" name="propName_keys[]" placeholder="Key">
            </div>
            <div class="col-md-4">
                <input class="form-control" type="text" name="propName_values[]" placeholder="Value">
            </div>
            <div class="col-md-4">
                <input class="form-control" type="text" name="propName_friendly[]" placeholder="Friendly Value">
            </div>
            <div class="col-md-2 del-col">
                <button type="button" class="btn btn-danger remove-item">Remove</button>
            </div>

        </div>
    </template>


@endsection
