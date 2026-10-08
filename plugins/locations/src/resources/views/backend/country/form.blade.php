@extends('cms::layouts.backend')


@section('content')
    @component('cms::components.form_resource', [
        'model' => $model,
    ])
        <div class="row">
            <div class="col-md-8">
                {{ Field::text($model, 'name') }}
                <div class="row">
                    <div class="col-md-6">
                        {{ Field::text($model, 'code', [
                            'label' => trans('cms::app.iso_code'),
                        ]) }}
                    </div>
                    <div class="col-md-6">
                        {{ Field::text($model, 'phonecode', [
                            'label' => trans('cms::app.country_code'),
                        ]) }}
                    </div>

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

                    <input type="hidden" name="has_states" value="0">
                    {{ Field::checkbox(trans_cms('cms::app.has_states'), 'has_states', [
                        'value' => 1,
                        'checked' => isset($model['has_states']) && $model['has_states'] == 1 ? true : false,
                    ]) }}
                @endcomponent
            </div>

        </div>
    @endcomponent
@endsection
