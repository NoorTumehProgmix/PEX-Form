{{ Field::select(trans('paymentMethods::content.mode'), 'data[mode]', [
    'value' => $data['mode'] ?? '',
    'options' => [
        'sandbox' => trans('paymentMethods::content.sandbox'),
        'live' => trans('paymentMethods::content.live'),
    ],
]) }}

{{ Field::text(trans('paymentMethods::content.sandbox_client_id'), 'data[sandbox_client_id]', [
    'value' => $data['sandbox_client_id'] ?? '',
]) }}

{{ Field::text(trans('paymentMethods::content.sandbox_secret'), 'data[sandbox_secret]', [
    'value' => $data['sandbox_secret'] ?? '',
]) }}

{{ Field::text(trans('paymentMethods::content.live_client_id'), 'data[live_client_id]', [
    'value' => $data['live_client_id'] ?? '',
]) }}

{{ Field::text(trans('paymentMethods::content.live_secret'), 'data[live_secret]', [
    'value' => $data['live_secret'] ?? '',
]) }}
