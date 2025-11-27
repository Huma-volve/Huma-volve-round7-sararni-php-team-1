<?php

namespace App\Services\Payments;

use App\Models\Booking;
use App\Models\Payment;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Services\Payments\PaymentGatewayInterface;


class PayPalService  implements PaymentGatewayInterface
{
    protected $client;

    public function __construct()
    {
        $this->client = new PayPalClient;
        $this->client->setApiCredentials(config('paypal'));
        $this->client->getAccessToken();

    }

    public function createOrder(array $data)
    {
      $response =   $this->client->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "cancel_url" => $data['cancel_url'],
                "return_url" => $data['return_url'] ,
            ],
            "purchase_units" => [[
                "description" => $data['description'],
                "amount" => [
                    "currency_code" => $data['currency'],
                    "value" => $data['amount']
                ]
            ]]
        ]);

        if (isset($response['id'])) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return [
                        'redirect_url' => $link['href']
                    ];
                }
            }
        }
        return $response;

    }

    public function captureOrder(string $paymentId, array $data = [])
    {
        $response = $this->client->capturePaymentOrder($paymentId);

        if (isset($response['status']) && $response['status'] === 'COMPLETED') {

         Payment::create([
                'user_id' => $data['user_id'] ?? 1,
                'item_id' => $data['item_id'],
                'category_id' => $data['category_id'],
                'payment_reference' => $paymentId,
                'amount' => $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'],
                'status' => $response['status'],
                'payment_method' => 'PayPal',
                'paid_at' => now()
            ]);

            $booking = Booking::find($data['item_id']);

                 if ($booking) {
                        $booking->markAsPaid('paypal', $paymentId);
                        $booking->confirm();
               };

              return $booking;
        };

    if (!empty($data['item_id'])) {
        $booking = Booking::find($data['item_id']);

        if ($booking) {
            $booking->cancel('Payment failed', 'system');
        }
    }

        return null;
    }
}
