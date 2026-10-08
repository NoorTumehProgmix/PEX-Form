<?php

use Progmix\PaymentMethods\Models\PaymentMethod;
use Progmix\PaymentMethods\Http\Resources\PaymentMethodCollectionResource;

if (!function_exists('get_payment_methods')) {
    function get_payment_methods(): array
    {
        $methods = PaymentMethod::active()->get();

        return (new PaymentMethodCollectionResource($methods))
            ->toArray(request());
    }
}
