<?php

namespace App\Services\Payments;

use App\Services\Payments\PaymentGatewayInterface;
use App\Services\Payments\PayPalService;    // Laravel لازم يلاقي الكلاس هنا
use App\Services\Payments\StripeService;

class PaymentFactory
{
    public static function make(string $gateway): PaymentGatewayInterface
    {
        return match ($gateway) {
            //   dd($gateway),
             'paypal' => new \App\Services\Payments\PayPalService(),
             'stripe' => new \App\Services\Payments\StripeService(),
            default => throw new \Exception("Unsupported payment gateway"),
        };
    }
}
