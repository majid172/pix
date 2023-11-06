<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    
    public function paymentStore(Request $request)
    {
        $min = 100000; // Smallest 6-digit number (100000)
        $max = 999999;
        $payment = new Payment();
        $payment->user_id = $request->user_id;
        $payment->order_id = $request->order_id;
        $payment->gateway = $request->gateway;
        $payment->amount = $request->amount;
        $payment->trx = rand($min,$max);
        $payment->save();

        $order = Order::find($request->order_id);
        $order->is_paid = ($order->is_paid == 0) ? 1:0;
        // $order->save();
        session()->put('trx',$payment->trx);
        return to_route('home.payment.confirm');
    }

    public function paymentConfirm()
    {
        $trx = session()->get('trx');
        $payment = Payment::where('trx',$trx)->with('user')->first();
        dd($payment->toArray());
        $dirname = $payment->gateway;
        $new = __NAMESPACE__.'\\'.$dirname.'\\ProcessController';
        $data = $new::process($payment);
        $data = json_decode($data);
        dd($data);
        if (isset($data->error)) {
            dd('eror');
            $notify[] = ['error', $data->message];
            return to_route('home')->withNotify($notify);
        }
        if (isset($data->redirect)) {
            dd('tr');
            return redirect($data->redirect_url);
        }
dd('ok');
        
    }
}
