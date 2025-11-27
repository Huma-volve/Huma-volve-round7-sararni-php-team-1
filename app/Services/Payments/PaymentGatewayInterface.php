<?php

namespace App\Services\Payments;

interface PaymentGatewayInterface
{
    public function createOrder(array $data);
    public function captureOrder(string $paymentId, array $data = []);
}
