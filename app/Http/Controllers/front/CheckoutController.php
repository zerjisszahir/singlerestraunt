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
use App\OrderDetails;
use App\Payment;
use App\User;
use App\Cart;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

class CheckoutController extends Controller
{

    /**
     * payment view
     */
    public function index()
    {
        return view('stripe-payment');
    }
    
    public function charge(Request $request)
    {
        try {

            $getuserdata=User::where('id',Session::get('id'))
            ->get()->first();

            $location=User::select('lat','lang','map')->where('type','1')->first();

            if ($request->order_type == "2") {
                $deal_lat=$location->lat;
                $deal_long=$location->lang;
            } else {
                $deal_lat=$request->lat;
                $deal_long=$request->lang;
            }

            if (env('Environment') == 'sendbox') {
                if ($request->order_type == "2") {
                    $delivery_charge = "0.00";
                    $address = 'New York, NY, USA';
                    $lat = '40.7127753';
                    $lang = '-74.0059728';
                    $building = "";
                    $landmark = "";
                    $pincode = '10001';
                    $city = @$city;
                    $state = @$state;
                    $country = @$country;
                    $order_total = $request->order_total-$request->$delivery_charge;
                } else {
                    $delivery_charge = $request->delivery_charge;
                    $address = 'New York, NY, USA';
                    $lat = '40.7127753';
                    $lang = '-74.0059728';
                    $order_total = $request->order_total;
                    $building = $request->building;
                    $landmark = $request->landmark;
                    $city = @$city;
                    $state = @$state;
                    $country = @$country;
                    $pincode = '10001';
                }
            } else{
                $gmapkey = $location->map;

                // Make the HTTP request
                $geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$deal_lat.','.$deal_long.'&sensor=false&key='.$gmapkey.'');

                $output= json_decode($geocode);
                $formattedAddress = @$output->results[0]->formatted_address;

                for($j=0;$j<count($output->results[0]->address_components);$j++){
                    $cn=array($output->results[0]->address_components[$j]->types[0]);
                    if(in_array("country", $cn)) {
                        $country = $output->results[0]->address_components[$j]->short_name;
                    }

                    if(in_array("postal_code", $cn)) {
                        $postal_code = $output->results[0]->address_components[$j]->long_name;
                    }

                    if(in_array("administrative_area_level_2", $cn)) {
                        $city = $output->results[0]->address_components[$j]->long_name;
                    }

                    if(in_array("administrative_area_level_1", $cn)) {
                        $state = $output->results[0]->address_components[$j]->short_name;
                    }
                }

                if ($request->order_type == "2") {
                    // dd($formattedAddress);
                    $delivery_charge = "0.00";
                    $address = $formattedAddress;
                    $lat = $deal_lat;
                    $lang = $deal_long;
                    $building = "";
                    $landmark = "";
                    $city = @$city;
                    $state = @$state;
                    $country = @$country;
                    $pincode = $postal_code;
                    $order_total = $request->order_total-$request->$delivery_charge;
                } else {
                    $delivery_charge = $request->delivery_charge;
                    $address = $formattedAddress;
                    $lat = $deal_lat;
                    $lang = $deal_long;
                    $order_total = $request->order_total;
                    $building = $request->building;
                    $landmark = $request->landmark;
                    $city = @$city;
                    $state = @$state;
                    $country = @$country;
                    $pincode = $postal_code;
                }
            }

            if ($request->discount_amount == "NaN") {
                $discount_amount = "0.00";
            } else {
                $discount_amount = $request->discount_amount;
            }

            $getpaymentdata=Payment::select('test_secret_key','live_secret_key','environment')->where('payment_name','Stripe')->first();

            if ($getpaymentdata->environment=='1') {
                $stripe_secret = $getpaymentdata->test_secret_key;
            } else {
                $stripe_secret = $getpaymentdata->live_secret_key;
            }

            Stripe::setApiKey($stripe_secret);

            if (env('Environment') == 'sendbox') {
                $customer = Customer::create(array(
                    'email' => $request->stripeEmail,
                    'source' => $request->stripeToken,
                    'name' => $getuserdata->name,
                    'address' => [
                        'line1' => 'New York, NY, USA',
                        'postal_code' => '10001',
                        'city' => 'New York',
                        'state' => 'NY',
                        'country' => 'US',
                    ],
                ));
            } else {
                $customer = Customer::create(array(
                    'email' => $request->stripeEmail,
                    'source' => $request->stripeToken,
                    'name' => $getuserdata->name,
                    'address' => [
                        'line1' => $address,
                        'postal_code' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'country' => $country,
                    ],
                ));
            }

            $charge = Charge::create(array(
                'customer' => $customer->id,
                'amount' => $order_total*100,
                'currency' => 'usd',
                'description' => 'Food Service',
            ));

            $order_number = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10);


            $order = new Order;
            $order->order_number =$order_number;
            $order->user_id =Session::get('id');
            $order->order_total =$order_total;
            $order->razorpay_payment_id =$charge['id'];
            $order->payment_type ='2';
            $order->order_type =$request->order_type;
            $order->status ='1';
            $order->address =$address;
            $order->building =$building;
            $order->landmark =$landmark;
            $order->pincode =$pincode;
            $order->lat =$lat;
            $order->lang =$lang;
            $order->promocode =$request->promocode;
            $order->discount_amount =$discount_amount;
            $order->discount_pr =$request->discount_pr;
            $order->tax =$request->tax;
            $order->tax_amount =$request->tax_amount;
            $order->delivery_charge =$delivery_charge;
            $order->order_notes =$request->notes;
            $order->order_from ='web';
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

            // return 'Charge successful, you get the course!';
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
    
}