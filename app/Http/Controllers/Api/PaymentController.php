<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\StripeClient;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request): JsonResponse
    {

        $request->validate([
            'currency' => 'sometimes|string|size:3',
            'payment_method' => 'sometimes|string',
            'item_id'  => 'required|integer',

        ]);

            $bookingId = Booking::where('id', $request->item_id)->first();

            $hotel  = Category::where('slug', 'hotels')->first();


        try {
            $paymentData =  [[
                    'price_data' => [
                        'currency' => $request->currency ?? 'usd',
                        'product_data' => [
                            'name' => 'Product name',
                        ],
                        'unit_amount' => (int) round($bookingId->total_price * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]] ;


            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            $checkoutSession = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => $paymentData,
                'mode' => 'payment',
                'success_url' =>url('/payment/confirm').'?session_id={CHECKOUT_SESSION_ID}&success=true',
                'cancel_url' =>url('/payment/cancel').'?session_id={CHECKOUT_SESSION_ID}',
            ]);


                $payment = new Payment();
                    $user_id  = 2 ;
                    $payment->user_id = $user_id;
                    $payment->category_id = $hotel->id;
                    $payment->amount = $bookingId->total_price;
                    $payment->item_id = $request->item_id;
                    $payment->payment_reference = $checkoutSession->id; // ★ مهم جداً
                    $payment->status = 'pending';
                    $payment->save();

             $session = $checkoutSession->url;

            return response()->json([
                'success' => true,
                'data' => $session
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment failed',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

//     public function paymentTestCallback(Request $request)
// {
//     dd([
//         '=== REQUEST DATA ===' => 'All incoming parameters',
//         'all_query_params' => $request->all(),
//         'full_url' => $request->fullUrl(),
//         'session_id' => $request->session_id,
//         'payment_success' => $request->payment_success,
//         'source' => $request->source,

//         '=== SERVER INFO ===' => 'Server and environment',
//         'method' => $request->method(),
//         'headers' => $request->headers->all(),
//         'ip' => $request->ip(),

//         '=== CHECK SESSION FROM STRIPE ===' => 'Fetching session data from Stripe',
//     ]);
// }

  public function confirmPayment(Request $request)
{

    $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

    $sessionId = $request->get('session_id');


        try{
                if (!$sessionId) {
                        return response()->json([
                        'success' => false,
                        'status'  => 'error',
                        'message' => 'Missing session_id'
                        ], 400);
                }

            $session  = $stripe->checkout->sessions->retrieve($sessionId);

            if(!$session){
                      return response()->json([
                          'success' => false,
                          'status'  => 'error',
                          'message' => 'Payment session not found',

                      ], 404);
                }

            $customer = $session->customer_details;


       $payment = Payment::where('payment_reference', $sessionId)->firstOrFail();

                if (!$payment) {
                    return response()->json([
                    'success' => false,
                    'status'  => 'not_found',
                    'message' => 'Payment record not found',
                    ], 404);
                }

                $payment->status = 'completed';
                $payment->paid_at = now();
                $payment->payment_method = 'stripe';
                $payment->payment_intent_id = $session->payment_intent;
                $payment->save();

                return response()->json([
                    'success'  => true,
                    'status'   => 'paid',
                    'message'  => 'Payment confirmed successfully',
                    'payment'  => $payment,
                    'customer' => $customer,
                ]);

        }catch(\Exception $e){
                return response()->json([
                    'success' => false,
                    'status'  => 'error',
                    'message' => 'Payment confirmation failed',
                    'error'   => $e->getMessage(),
                ], 500);
        }
}


    public function cancelPayment(Request $request)
    {
            $sessionId = $request->get('session_id');

                if (!$sessionId) {
                    return response()->json([
                    'success' => false,
                    'status'  => 'error',
                    'message' => 'Invalid payment session.'
                    ], 400);
                }

        $payment = Payment::where('payment_reference', $sessionId)->first();

                if (!$payment) {
                    return response()->json([
                    'success' => false,
                    'status'  => 'not_found',
                    'message' => 'Payment not found.'
                    ], 404);
                }

                 $payment->status = 'failed';
                 $payment->save();

            return response()->json([
                'success' => true,
                'status'  => 'failed',
                'message' => 'Payment was failed.',
                'payment_id' => $payment->id,
                'payment' => $payment
            ]);


    }

    // public function getPayment(string $paymentIntentId): JsonResponse
    // {
    //     try {
    //         $result = $this->paymentService->getPaymentStatus($paymentIntentId);

    //         return response()->json([
    //             'success' => $result['success'],
    //             'data' => $result,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve payment',
    //             'error' => $e->getMessage(),
    //         ], 422);
    //     }
    // }




    }
