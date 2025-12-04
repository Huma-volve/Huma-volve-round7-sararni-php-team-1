<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Payment;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use function Pest\Laravel\session;
use App\Services\Payments\PaymentFactory;

class PaymentController extends Controller
{


    public function createPayment(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'gateway' => 'required|string', // 'paypal' أو 'stripe'
        ]);

        $booking = Booking::find($request->item_id);
        $category = Category::where('slug', 'hotels')->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }


        $gateway = PaymentFactory::make($request->gateway);


        $session = $gateway->createOrder([
            'amount' => $booking->total_price,
            'currency' => $booking->currency,
            'description' => $category->name,

            'return_url' => route('payment.success', [
                'booking_id' => $booking->id,
                'category_id' => $category->id,
                'gateway' => $request->gateway ,
            ]),

            'cancel_url' => route('payment.cancel', ['gateway' => $request->gateway]),
            'user_id' => 1,
            'item_id' => $booking->id,
            'category_id' => $category->id
        ]);

        return response()->json([
            'success' => true,
            'data' => $session
        ]);
    }

    public function paymentSuccess(Request $request)
    {
        $request->validate([
            'booking_id' => 'required',
            'category_id' => 'required',
            'gateway' => 'required|string'
        ]);

        $gateway = PaymentFactory::make($request->gateway);

        $paymentId = $request->get('token') ?? $request->get('session_id');

            if (!$paymentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment ID is missing.'
                ], 400);
            }


        $payment = $gateway->captureOrder($request->get('token') ?? $request->get('session_id'), [
            'user_id' => 1,
            'item_id' => $request->booking_id,
            'category_id' => $request->category_id
        ]);




        if ($payment) {
            return response()->json(['success' => true, 'message' => 'Payment successful', 'payment' => $payment]);
        }

        return response()->json(['success' => false, 'message' => 'Payment failed'], 500);
    }

    public function paymentCancel(Request $request)
    {
        $gateway = PaymentFactory::make($request->gateway);


        return response()->json(['success' => true, 'message' => 'Payment cancelled']);
    }
}

