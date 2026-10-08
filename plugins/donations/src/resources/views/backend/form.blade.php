@extends('cms::layouts.backend')

@section('content')
    @component('cms::components.form_resource', [
        'model' => $model,
    ])
        <div class="row appointment_form">
            <div class="col-md-8">
                @component('cms::components.card', [
                    'label' => trans('donations::content.donations_info'),
                ])
                    <div class="row mb-2">
                        <div class="col-md-12">
                            {{ Field::text($model, 'name', ['disabled' => true, 'label' => __('donations::content.name')]) }}
                            {{ Field::text($model, 'email', ['disabled' => true, 'label' => __('donations::content.email')]) }}
                            {{ Field::text($model, 'phone', ['disabled' => true, 'label' => __('donations::content.phone')]) }}
                            {{ Field::text($model, 'payment_method', ['disabled' => true, 'label' => __('donations::content.payment_method')]) }}
                            {{ Field::text($model, 'payment_status', ['disabled' => true, 'label' => __('donations::content.payment_status')]) }}
                            {{ Field::text($model, 'payment_id', ['disabled' => true, 'label' => __('donations::content.payment_id')]) }}
                            {{ Field::text($model, 'payment_amount', ['disabled' => true, 'label' => __('donations::content.payment_amount')]) }}
                            {{ Field::text($model, 'payment_currency', ['disabled' => true, 'label' => __('donations::content.payment_currency')]) }}
                            {{ Field::text($model, 'recurring', ['disabled' => true, 'label' => __('donations::content.recurring')]) }}
                            {{ Field::text($model, 'recurring_amount', ['disabled' => true, 'label' => __('donations::content.recurring_amount')]) }}
                            {{ Field::text($model, 'recurring_interval', ['disabled' => true, 'label' => __('donations::content.recurring_interval')]) }}
                            {{ Field::textarea($model, 'note', ['disabled' => true, 'label' => __('donations::content.note')]) }}
                            {{ Field::text($model, 'created_at', ['disabled' => true, 'label' => __('cms::app.date'), 'value' => date_format(Carbon\Carbon::parse($model->created_at), 'F j, Y, g:i a')]) }}
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>
    @endcomponent
@endsection
