@extends('cms::layouts.backend')

@section('content')
    @component('cms::components.form_resource', [
        'model' => $model,
    ])
        <div class="row appointment_form">
            <div class="col-md-8">
                @component('cms::components.card', [
                    'label' => trans('forum-registration::content.registration_info'),
                ])
                    <div class="row mb-2">
                        <div class="col-md-12">
                            {{ Field::text($model, 'name', ['disabled' => true, 'label' => __('forum-registration::content.name')]) }}
                            {{ Field::text($model, 'institution', ['disabled' => true, 'label' => __('forum-registration::content.institution')]) }}
                            {{ Field::text($model, 'job_title', ['disabled' => true, 'label' => __('forum-registration::content.job_title')]) }}
                            {{ Field::text($model, 'email', ['disabled' => true, 'label' => __('forum-registration::content.email')]) }}
                            {{ Field::text($model, 'phone', ['disabled' => true, 'label' => __('forum-registration::content.phone')]) }}
                            {{ Field::text($model, 'part_type', ['disabled' => true, 'label' => __('forum-registration::content.part_type')]) }}
                            {{ Field::text($model, 'sponsor_type', ['disabled' => true, 'label' => __('forum-registration::content.sponsor_type')]) }}
                            {{ Field::text($model, 'created_at', ['disabled' => true, 'label' => __('cms::app.date'), 'value' => date_format(Carbon\Carbon::parse($model->created_at), 'F j, Y, g:i a')]) }}
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>
    @endcomponent
@endsection
