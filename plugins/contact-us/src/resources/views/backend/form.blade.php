@extends('cms::layouts.backend')

@section('content')
    @component('cms::components.form_resource', [
    'model' => $model,
    ])
        <div class="row appointment_form">
            <div class="col-md-8">
                @component('cms::components.card', [
                'label' => trans('contact::content.contact_info'),
                ])
                    <div class="row mb-2">
                        <div class="col-md-12">
                            {{ Field::text($model, 'name', ['disabled' => true,'label'=>__('contact::content.name')]) }}
                            {{ Field::text($model, 'email', ['disabled' => true,'label'=>__('contact::content.email')]) }}
                            {{ Field::text($model, 'phone', ['disabled' => true,'label'=>__('contact::content.phone')]) }}
                            {{ Field::text($model, 'subject', ['disabled' => true,'label'=>__('contact::content.subject')]) }}
                            {{ Field::textarea($model, 'message', ['disabled' => true,'label'=>__('contact::content.message')]) }}
                            {{ Field::text($model, 'created_at', ['disabled' => true,'label'=>__('cms::app.date'),'value'=>date_format(Carbon\Carbon::parse($model->created_at),'F j, Y, g:i a')]) }}
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>
    @endcomponent
@endsection
