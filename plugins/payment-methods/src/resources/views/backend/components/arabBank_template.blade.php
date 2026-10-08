{{ Field::select(trans('paymentMethods::content.mode'), 'data[mode]', [
    'value' => $data['mode'] ?? '',
    'options' => [
        'sandbox' => trans('paymentMethods::content.sandbox'),
        'live' => trans('paymentMethods::content.live'),
    ],
]) }}

{{ Field::text(trans('paymentMethods::content.access_key'), 'data[access_key]', [
    'value' => $data['access_key'] ?? '',
]) }}

{{ Field::text(trans('paymentMethods::content.profile_id'), 'data[profile_id]', [
    'value' => $data['profile_id'] ?? '',
]) }}

{{ Field::text(trans('paymentMethods::content.secret_key'), 'data[secret_key]', [
    'value' => $data['secret_key'] ?? '',
]) }}

{{ Field::text(trans('paymentMethods::content.production_server'), 'data[production_server]', [
    'value' => $data['production_server'] ?? '',
]) }}

{{ Field::text(trans('paymentMethods::content.development_server'), 'data[development_server]', [
    'value' => $data['development_server'] ?? '',
]) }}
