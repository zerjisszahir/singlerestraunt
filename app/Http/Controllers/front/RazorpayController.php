<?php
namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Session;
use App\Order;
use App\User;
use App\Payment;
use App\OrderDetails;
use App\Cart;
use Redirect;

class RazorpayController extends Controller
{    
    public function payWithRazorpay()
    {        
        return view('front.payWithRazorpay');
    }

    public function payment(Request $request)
    {
        
        //Input items of form
        $input = $request->all();

        $getpaymentdata=Payment::select('test_public_key','live_public_key','test_secret_key','live_secret_key','environment')->where('payment_name','RazorPay')->first();

        if ($getpaymentdata->environment=='1') {
            $razor_secret = $getpaymentdata->test_secret_key;
        } else {
            $razor_secret = $getpaymentdata->live_secret_key;
        }

        if ($getpaymentdata->environment=='1') {
            $razor_public = $getpaymentdata->test_public_key;
        } else {
            $razor_public = $getpaymentdata->live_public_key;
        }

        //get API Configuration 
        $api = new Api($razor_public, $razor_secret);
        //Fetch payment information by razorpay_payment_id
        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        $getuserdata=User::where('id',Session::get('id'))
        ->get()->first(); 

        if(count($input)  && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount'=>$payment['amount']));

                $order_number = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10);

                if ($request->order_type == "2") {
                    $delivery_charge = "0.00";
                    $address = "";
                    $lat = "";
                    $lang = "";
                    $building = "";
                    $landmark = "";
                    $postal_code = "";
                    $order_total = $request->order_total-$request->$delivery_charge;
                } else {
                    $delivery_charge = $request->delivery_charge;
                    $address = $request->address;
                    $lat = $request->lat;
                    $lang = $request->lang;
                    $order_total = $request->order_total;
                    $building = $request->building;
                    $landmark = $request->landmark;
                    $postal_code = $request->postal_code;
                }

                if ($request->discount_amount == "NaN") {
                    $discount_amount = "0.00";
                } else {
                    $discount_amount = $request->discount_amount;
                }

                $order = new Order;
                $order->order_number = $order_number;
                $order->user_id = Session::get('id');
                $order->order_total =$request->order_total;
                $order->razorpay_payment_id =$request->razorpay_payment_id;
                $order->payment_type ='1';
                $order->status ='1';
                $order->address =$address;
                $order->promocode =$request->promocode;
                $order->discount_amount =$discount_amount;
                $order->discount_pr =$request->discount_pr;
                $order->tax =$request->tax;
                $order->tax_amount =$request->tax_amount;
                $order->delivery_charge =$request->delivery_charge;
                $order->order_notes =$request->notes;
                $order->order_from ='web';
                $order->order_type =$request->order_type;
                $order->lat =$lat;
                $order->lang =$lang;
                $order->building =$building;
                $order->landmark =$landmark;
                $order->pincode =$postal_code;
                $order->save();

                $order_id = DB::getPdo()->lastInsertId();
                $data=Cart::where('cart.user_id',Session::get('id'))
                ->get();

                foreach ($data as $value) {
                    $OrderPro = new OrderDetails;
                    $OrderPro->order_id = $order_id;
                    $OrderPro->user_id = $value['user_id'];
                    $OrderPro->item_id = $value['item_id'];
                    $OrderPro->price = $value['price'];
                    $OrderPro->qty = $value['qty'];
                    $OrderPro->item_notes = $value['item_notes'];
                    $OrderPro->save();
                }
                $cart=Cart::where('user_id', Session::get('id'))->delete();

                $count=Cart::where('user_id',Session::get('id'))->count();

                try{
                    $ordermessage='Order "'.$order_number.'" has been placed';
                    $email=$getuserdata->email;
                    $name=$getuserdata->name;
                    $data=['ordermessage'=>$ordermessage,'email'=>$email,'name'=>$name];

                    Mail::send('Email.orderemail',$data,function($message)use($data){
                        $message->from(env('MAIL_USERNAME'))->subject($data['ordermessage']);
                        $message->to($data['email']);
                    } );
                }catch(\Swift_TransportException $e){
                    $response = $e->getMessage() ;
                    return response()->json(['status'=>0,'message'=>'Something went wrong while sending email Please try again...'],200);
                }
                
                Session::put('cart', $count);

                session()->forget(['offer_amount','offer_code']);

                return response()->json(['status'=>1,'message'=>'Order has been placed'],200); 

            } catch (\Exception $e) {
                return  $e->getMessage();
                \Session::put('error',$e->getMessage());
                return redirect()->back();
            }

            // Do something here for store payment details in database...
        }
        
        \Session::put('success', 'Payment successful');
        return redirect()->back();
    }
}