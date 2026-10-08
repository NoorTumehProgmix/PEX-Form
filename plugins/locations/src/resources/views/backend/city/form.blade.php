@extends('cms::layouts.backend')


@section('content')
    @component('cms::components.form_resource', [
        'model' => $model,
    ])
        <div class="row">
            <div class="col-md-8">
                {{ Field::text($model, 'name') }}
                <div class="form-group">
                    <label class="col-form-label" for="state_id">{{ trans('cms::app.city') }}</label>
                    <select name="state_id" id="state_id" class="form-control select2">
                        @foreach ($states as $state)
                            <option value="{{ $state->id }}" {{ $state->id == $model->state_id ? 'selected' : '' }}>
                                {{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
    @endcomponent
@endsection
