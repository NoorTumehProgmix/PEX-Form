@extends('cms::layouts.backend')

@section('content')
    @component('cms::components.form_resource', [
        'model' => $model,
    ])
        <div class="row appointment_form">
            <div class="col-md-8">
                @component('cms::components.card', [
                    'label' => trans('SearchLog::content.name'),
                ])
                    <div class="row mb-2">
                        <div class="col-md-12">
                            {{ Field::text($model, 'text', ['disabled' => true,'label' => trans('SearchLog::content.text')]) }}
                            {{ Field::text($model, 'lang', ['disabled' => true,'label' => trans('cms::app.lang')]) }}
                            {{ Field::text($model, 'ip_address', ['disabled' => true,'label' => trans('SearchLog::content.ip_address')]) }}
                            {{ Field::textarea($model, 'data', ['disabled' => true,'label' => trans('SearchLog::content.data')]) }}
                            {{ Field::text($model, 'created_at', ['disabled' => true, 'value' => jw_date_format($model->created_at), 'label' => trans('cms::app.date')]) }}
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>
    @endcomponent
@endsection
