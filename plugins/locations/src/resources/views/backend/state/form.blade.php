@extends('cms::layouts.backend')


@section('content')
    @component('cms::components.form_resource', [
        'model' => $model,
    ])
        <div class="row">
            <div class="col-md-8">
                {{ Field::text($model, 'name') }}
                <div class="form-group">
                    <label class="col-form-label" for="country_id">{{ trans('cms::app.country') }}</label>
                    <select name="country_id" id="country_id" class="form-control select2">
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" {{ $country->id == $model->country_id ? 'selected' : '' }}>

                                {{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <input type="hidden" name="active" value="0">
                @component('cms::components.card', [
                    'label' => trans('cms::app.status'),
                ])
                    {{ Field::checkbox($model, 'active', [
                        'checked' => $model->active == 1 || is_null($model->active),
                    ]) }}
                @endcomponent
            </div>

        </div>
    @endcomponent
@endsection
