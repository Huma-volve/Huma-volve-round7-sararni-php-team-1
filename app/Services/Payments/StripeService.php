<?php

namespace App\Services\Payments;

use App\Models\Booking;
use App\Models\Payment;
use Stripe\StripeClient;

class StripeService implements PaymentGatewayInterface
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    public function createOrder(array $data)
    {
        // جهز اسم المنتج
        $productName = $data['product_name'] ?? 'Booking #' . $data['item_id'];

        $session = $this->stripe->checkout->sessions->create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $data['currency'] ?? 'usd',
                        'product_data' => [
                            'name' => $productName,
                        ],
                        'unit_amount' => (int) round($data['amount'] * 100),
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => $data['return_url'] . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $data['cancel_url'],
        ]);

        // سجّل الدفعة
       $booking = Booking::find($data['item_id']);
        if ($booking) {
            $booking->payment_reference = $session->id;
            $booking->save();
        }



        return $session->url;

    }

    public function captureOrder(string $paymentId, array $data = [])
    {
        $session = $this->stripe->checkout->sessions->retrieve($paymentId);

        $booking = Booking::where('payment_reference', $paymentId)->first();
        if ($booking) {
            $booking->markAsPaid('stripe', $paymentId);
            $booking->confirm();

         Payment::create([
                'user_id' => $data['user_id'] ?? 1,
                'item_id' => $booking->id,
                'category_id' => $data['category_id'],
                'amount' => $session->amount_total / 100,
                'status' => 'completed',
                'payment_method' => 'Stripe',
                'paid_at' => now()
            ]);

            return $booking;
        }




    if (!empty($data['item_id'])) {
        $booking = Booking::find($data['item_id']);

        if ($booking) {
            $booking->cancel('Payment failed', 'system');
        }
         return null;
    }

    }
}
