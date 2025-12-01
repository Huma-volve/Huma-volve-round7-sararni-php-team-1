<?php

namespace App\Http\Controllers\Api\V1;

use App\Interfaces\PaymentGatewayInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CarPaymentController extends Controller
{
    //
    protected PaymentGatewayInterface $paymentGateway; 

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function paymentProcess(Request $request){
        return $this->paymentGateway->sendPayment($request);
    }

    public function callBack(Request $request){
        $response = $this->paymentGateway->callBack($request);

        if($response){
            // save data in database
            return redirect()->route('payment.success');
        }else{
            return redirect()->route('payment.failed');
        }
    }

    public function success(){
        return view('payment-success');
    }

    public function failed(){
        return view('payment-failed');
    }

}
