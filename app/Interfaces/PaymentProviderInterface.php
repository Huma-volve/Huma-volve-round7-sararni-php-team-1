<?php

namespace App\Interfaces;

interface PaymentProviderInterface
{
    public function createPaymentIntent(array $data): array;

    public function confirmPayment(string $paymentIntentId): array;

    public function cancelPayment(string $paymentIntentId): array;

    public function getPayment(string $paymentIntentId): array;
}
