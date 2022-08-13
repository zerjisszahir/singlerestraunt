<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Cart;
use App\Order;
use App\User;
use App\OrderDetails;
use App\Promocode;
use App\ItemImages;
use App\Addons;
use App\Pincode;
use App\Payment;
use App\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Validator;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

class CheckoutController extends Controller
{
    public function summary(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User ID is required"],400);
        }

        $cartdata=Cart::with('itemimage')->select('cart.id','cart.qty','cart.price as total_price','cart.item_notes','item.item_name','item.item_price','cart.item_id','cart.addons_id')
        ->join('item','cart.item_id','=','item.id')
        ->where('cart.is_available','1')
        ->where('cart.user_id',$request->user_id)->get()->toArray();

        $taxval=User::select('users.tax','users.delivery_charge')->where('users.id','1')
        ->get()->first();

        foreach ($cartdata as $value) {

            $arr = explode(',', $value['addons_id']);
            $addons = Addons::whereIn('id',$arr)->get();

            // $images = ItemImages::where('id',$value['item_id'])->get();

        	$data[] = array(
        	    "id" => $value['id'],
        	    "qty" => $value['qty'],
        	    "total_price" => $value['total_price'],
        	    "item_name" => $value['item_name'],
        	    "item_price" => $value['item_price'],
        	    "item_id" => $value['item_id'],
                "item_notes" => $value['item_notes'],
                "addons" => $addons,
                "itemimage" => $value["itemimage"],
        	);
        }

        @$order_total = array_sum(array_column(@$data, 'total_price'));
        $summery = array(
        	'order_total' => "$order_total",
        	'tax' => "$taxval->tax", 
        	'delivery_charge' => "$taxval->delivery_charge", 
        );
        
        if(!empty($cartdata))
        {
            return response()->json(['status'=>1,'message'=>'Summery list Successful','data'=>@$data,'summery'=>$summery],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'No data found'],200);
        }
    }

    public function order(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User ID is required"],400);
        }
        if($request->order_total == ""){
            return response()->json(["status"=>0,"message"=>"Total Amount is required"],400);
        }
        
        if($request->payment_type == ""){
            return response()->json(["status"=>0,"message"=>"Payment Type is required"],400);
        }

        $order_number = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10);
        $getdata=User::select('firebase','map')->where('type','1')->first();

        $getstripe=Payment::select('environment','test_secret_key','live_secret_key')->where('payment_name','Stripe')->first();

        if ($getstripe->environment == "1") {
            $skey = $getstripe->test_secret_key;
        } else {
            $skey = $getstripe->live_secret_key;
        }

    	try {

    	    if($request->payment_type == "1") {

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

                    if($request->address == ""){
                        return response()->json(["status"=>0,"message"=>"Address is required"],400);
                    }

                    if($request->lat == ""){
                        return response()->json(["status"=>0,"message"=>"Please select the address from suggestion"],400);
                    }

                    if($request->lang == ""){
                        return response()->json(["status"=>0,"message"=>"Please select the address from suggestion"],400);
                    }

                    if($request->pincode == ""){
                        return response()->json(["status"=>0,"message"=>"Pincode is required"],400);
                    }

                    if($request->building == ""){
                        return response()->json(["status"=>0,"message"=>"Door / Flat No. is required"],400);
                    }

                    if($request->landmark == ""){
                        return response()->json(["status"=>0,"message"=>"Landmark is required"],400);
                    }

                    $delivery_charge = $request->delivery_charge;
                    $address = $request->address;
                    $lat = $request->lat;
                    $lang = $request->lang;
                    $order_total = $request->order_total;
                    $building = $request->building;
                    $landmark = $request->landmark;
                    $postal_code = $request->pincode;
                }

    	    	$order = new Order;
    	    	$order->order_number =$order_number;
    	    	$order->user_id =$request->user_id;
    	    	$order->order_total =$order_total;
    	    	$order->razorpay_payment_id =$request->razorpay_payment_id;
    	    	$order->payment_type =$request->payment_type;
                $order->order_type =$request->order_type;
                $order->status ='1';
    	    	$order->address =$address;
                $order->pincode =$postal_code;
                $order->building =$building;
                $order->landmark =$landmark;
                $order->lat =$lat;
                $order->lang =$lang;
                $order->promocode =$request->promocode;
                $order->discount_amount =$request->discount_amount;
                $order->discount_pr =$request->discount_pr;
                $order->tax =$request->tax;
                $order->tax_amount =$request->tax_amount;
                $order->delivery_charge =$delivery_charge;
                $order->order_notes =$request->order_notes;
                $order->order_from =$request->order_from;
    	    	$order->save();

    	    	$order_id = DB::getPdo()->lastInsertId();
    	    	$data=Cart::where('cart.user_id',$request['user_id'])
    	    	->get();

    	    	foreach ($data as $value) {
    	    	    $OrderPro = new OrderDetails;
    	    	    $OrderPro->order_id = $order_id;
                    $OrderPro->user_id = $value['user_id'];
    	    	    $OrderPro->item_id = $value['item_id'];
    	    	    $OrderPro->price = $value['price'];
    	    	    $OrderPro->qty = $value['qty'];
                    $OrderPro->item_notes = $value['item_notes'];
                    $OrderPro->addons_id = $value['addons_id'];
    	    	    $OrderPro->save();
    	    	}
    	    	$cart=Cart::where('user_id', $request->user_id)->delete();


                //Notification
                $getalluses=User::select('token','email','name')->where('id',$request->user_id)
                ->get()->first();

                try{
                    $email=$getalluses->email;
                    $name=$getalluses->name;
                    $ordermessage='Order "'.$order_number.'" has been placed';
                    $data=['ordermessage'=>$ordermessage,'email'=>$email,'name'=>$name];

                    Mail::send('Email.orderemail',$data,function($message)use($data){
                        $message->from(env('MAIL_USERNAME'))->subject($data['ordermessage']);
                        $message->to($data['email']);
                    } );

                    $title = "Order";
                    $body = 'Order "'.$order_number.'" has been placed';
                    $google_api_key = $getdata->firebase; 
                    
                    $registrationIds = $getalluses->token;
                    #prep the bundle
                    $msg = array
                        (
                        'body'  => $body,
                        'title' => $title,
                        'sound' => 1/*Default sound*/
                        );
                    $fields = array
                        (
                        'to'            => $registrationIds,
                        'notification'  => $msg
                        );
                    $headers = array
                        (
                        'Authorization: key=' . $google_api_key,
                        'Content-Type: application/json'
                        );
                    #Send Reponse To FireBase Server
                    $ch = curl_init();
                    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                    curl_setopt( $ch,CURLOPT_POST, true );
                    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

                    $result = curl_exec ( $ch );
                    curl_close ( $ch );
                }catch(\Swift_TransportException $e){
                    $response = $e->getMessage() ;
                    // return Redirect::back()->with('danger', $response);
                    return response()->json(['status'=>0,'message'=>'Something went wrong while sending email Please try again...'],200);
                }

    	    	return response()->json(['status'=>1,'message'=>'Order has been placed'],200);

            } elseif ($request->payment_type == "2") {

                $getuserdata=User::select('name')->where('id',$request->user_id)
                ->get()->first();

                $location=User::select('lat','lang')->where('type','1')->first();

                if ($request->order_type == "2") {
                    $deal_lat=$location->lat;
                    $deal_long=$location->lang;
                } else {
                    $deal_lat=$request->lat;
                    $deal_long=$request->lang;
                }
                // $gmapkey = $getdata->map;

                // // Make the HTTP request
                // $geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$deal_lat.','.$deal_long.'&sensor=false&key='.$gmapkey.'');

                // $output= json_decode($geocode);
                // $formattedAddress = @$output->results[0]->formatted_address;

                // for($j=0;$j<count($output->results[0]->address_components);$j++){
                //     $cn=array($output->results[0]->address_components[$j]->types[0]);
                //     if(in_array("country", $cn)) {
                //         $country = $output->results[0]->address_components[$j]->short_name;
                //     }

                //     if(in_array("postal_code", $cn)) {
                //         $postal_code = $output->results[0]->address_components[$j]->long_name;
                //     }

                //     if(in_array("administrative_area_level_2", $cn)) {
                //         $city = $output->results[0]->address_components[$j]->long_name;
                //     }

                //     if(in_array("administrative_area_level_1", $cn)) {
                //         $state = $output->results[0]->address_components[$j]->short_name;
                //     }
                // }

                if ($request->order_type == "2") {                    
                    $delivery_charge = "0.00";
	                $address = 'New York, NY, USA';
	                $lat = '40.7127753';
	                $lang = '-74.0059728';
	                $building = "";
	                $landmark = "";
	                $postal_code = '10001';
	                $city = @$city;
	                $state = @$state;
	                $country = @$country;
	                $order_total = $request->order_total-$request->$delivery_charge;
                } else {

                    if($request->address == ""){
                        return response()->json(["status"=>0,"message"=>"Address is required"],400);
                    }

                    if($request->lat == ""){
                        return response()->json(["status"=>0,"message"=>"Please select the address from suggestion"],400);
                    }

                    if($request->lang == ""){
                        return response()->json(["status"=>0,"message"=>"Please select the address from suggestion"],400);
                    }

                    if($request->pincode == ""){
                        return response()->json(["status"=>0,"message"=>"Pincode is required"],400);
                    }

                    if($request->building == ""){
                        return response()->json(["status"=>0,"message"=>"Door / Flat No. is required"],400);
                    }

                    if($request->landmark == ""){
                        return response()->json(["status"=>0,"message"=>"Landmark is required"],400);
                    }

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
	                $postal_code = '10001';
                }


                Stripe::setApiKey($skey);

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

                $charge = Charge::create(array(
                    'customer' => $customer->id,
                    'amount' => $order_total*100,
                    'currency' => 'usd',
                    'description' => 'Food Service',
                ));

                $order = new Order;
                $order->order_number =$order_number;
                $order->user_id =$request->user_id;
                $order->order_total =$order_total;
                $order->razorpay_payment_id =$charge['id'];
                $order->payment_type =$request->payment_type;
                $order->order_type =$request->order_type;
                $order->status ='1';
                $order->address =$address;
                $order->building =$building;
                $order->landmark =$landmark;
                $order->pincode =$postal_code;
                $order->lat =$lat;
                $order->lang =$lang;
                $order->promocode =$request->promocode;
                $order->discount_amount =$request->discount_amount;
                $order->discount_pr =$request->discount_pr;
                $order->tax =$request->tax;
                $order->tax_amount =$request->tax_amount;
                $order->delivery_charge =$delivery_charge;
                $order->order_notes =$request->order_notes;
                $order->order_from =$request->order_from;
                $order->save();

                $order_id = DB::getPdo()->lastInsertId();
                $data=Cart::where('cart.user_id',$request['user_id'])
                ->get();

                foreach ($data as $value) {
                    $OrderPro = new OrderDetails;
                    $OrderPro->order_id = $order_id;
                    $OrderPro->user_id = $value['user_id'];
                    $OrderPro->item_id = $value['item_id'];
                    $OrderPro->price = $value['price'];
                    $OrderPro->qty = $value['qty'];
                    $OrderPro->item_notes = $value['item_notes'];
                    $OrderPro->addons_id = $value['addons_id'];
                    $OrderPro->save();
                }
                $cart=Cart::where('user_id', $request->user_id)->delete();


                //Notification
                $getalluses=User::select('token','email','name')->where('id',$request->user_id)
                ->get()->first();

                try{
                    $ordermessage='Order "'.$order_number.'" has been placed';
                    $email=$getalluses->email;
                    $name=$getalluses->name;
                    $data=['ordermessage'=>$ordermessage,'email'=>$email,'name'=>$name];

                    Mail::send('Email.orderemail',$data,function($message)use($data){
                        $message->from(env('MAIL_USERNAME'))->subject($data['ordermessage']);
                        $message->to($data['email']);
                    } );

                    $title = "Order";
                    $body = 'Order "'.$order_number.'" has been placed';
                    $google_api_key = $getdata->firebase; 
                    
                    $registrationIds = $getalluses->token;
                    #prep the bundle
                    $msg = array
                        (
                        'body'  => $body,
                        'title' => $title,
                        'sound' => 1/*Default sound*/
                        );
                    $fields = array
                        (
                        'to'            => $registrationIds,
                        'notification'  => $msg
                        );
                    $headers = array
                        (
                        'Authorization: key=' . $google_api_key,
                        'Content-Type: application/json'
                        );
                    #Send Reponse To FireBase Server
                    $ch = curl_init();
                    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                    curl_setopt( $ch,CURLOPT_POST, true );
                    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

                    $result = curl_exec ( $ch );
                    // dd($result);
                    curl_close ( $ch );
                }catch(\Swift_TransportException $e){
                    $response = $e->getMessage() ;
                    // return Redirect::back()->with('danger', $response);
                    return response()->json(['status'=>0,'message'=>'Something went wrong while sending email Please try again...'],200);
                }

                return response()->json(['status'=>1,'message'=>'Order has been placed'],200);
            } elseif ($request->payment_type == "3") {

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

                    if($request->address == ""){
                        return response()->json(["status"=>0,"message"=>"Address is required"],400);
                    }

                    if($request->lat == ""){
                        return response()->json(["status"=>0,"message"=>"Please select the address from suggestion"],400);
                    }

                    if($request->lang == ""){
                        return response()->json(["status"=>0,"message"=>"Please select the address from suggestion"],400);
                    }

                    if($request->pincode == ""){
                        return response()->json(["status"=>0,"message"=>"Pincode is required"],400);
                    }

                    if($request->building == ""){
                        return response()->json(["status"=>0,"message"=>"Door / Flat No. is required"],400);
                    }

                    if($request->landmark == ""){
                        return response()->json(["status"=>0,"message"=>"Landmark is required"],400);
                    }

                    $delivery_charge = $request->delivery_charge;
                    $address = $request->address;
                    $lat = $request->lat;
                    $lang = $request->lang;
                    $order_total = $request->order_total;
                    $building = $request->building;
                    $landmark = $request->landmark;
                    $postal_code = $request->pincode;
                }

                $order = new Order;
                $order->order_number =$order_number;
                $order->user_id =$request->user_id;
                $order->order_total =$order_total;
                $order->payment_type =$request->payment_type;
                $order->order_type =$request->order_type;
                $order->status ='1';
                $order->address =$address;
                $order->building =$building;
                $order->landmark =$landmark;
                $order->pincode =$postal_code;
                $order->lat =$lat;
                $order->lang =$lang;
                $order->promocode =$request->promocode;
                $order->discount_amount =$request->discount_amount;
                $order->discount_pr =$request->discount_pr;
                $order->tax =$request->tax;
                $order->tax_amount =$request->tax_amount;
                $order->delivery_charge =$delivery_charge;
                $order->order_notes =$request->order_notes;
                $order->order_from =$request->order_from;
                $order->save();


                $order_id = DB::getPdo()->lastInsertId();
                $data=Cart::where('cart.user_id',$request['user_id'])
                ->get();
                foreach ($data as $value) {
                    $OrderPro = new OrderDetails;
                    $OrderPro->order_id = $order_id;
                    $OrderPro->user_id = $value['user_id'];
                    $OrderPro->item_id = $value['item_id'];
                    $OrderPro->price = $value['price'];
                    $OrderPro->qty = $value['qty'];
                    $OrderPro->item_notes = $value['item_notes'];
                    $OrderPro->addons_id = $value['addons_id'];
                    $OrderPro->save();
                    
                }
                $cart=Cart::where('user_id', $request->user_id)->delete();

                // $walletdata=Transaction::select('wallet')->where('user_id',$request->user_id)->sum('wallet');

                $getalluses=User::select('token','email','name','wallet')->where('id',$request->user_id)
                ->get()->first();

                $wallet = $getalluses->wallet - $order_total;

                $UpdateWalletDetails = User::where('id', $request->user_id)
                ->update(['wallet' => $wallet]);

                $Wallet = new Transaction;
                $Wallet->user_id = $request->user_id;
                $Wallet->order_id = $order_id;
                $Wallet->order_number = $order_number;
                $Wallet->wallet = $order_total;
                $Wallet->payment_id = NULL;
                $Wallet->order_type = $request->order_type;
                $Wallet->transaction_type = '2';
                $Wallet->save();

                //Notification
                

                $title = "Order";
                $body = 'Order "'.$order_number.'" has been placed';
                $google_api_key = $getdata->firebase; 
                
                $registrationIds = $getalluses->token;
                #prep the bundle
                $msg = array
                    (
                    'body'  => $body,
                    'title' => $title,
                    'sound' => 1/*Default sound*/
                    );
                $fields = array
                    (
                    'to'            => $registrationIds,
                    'notification'  => $msg
                    );
                $headers = array
                    (
                    'Authorization: key=' . $google_api_key,
                    'Content-Type: application/json'
                    );
                #Send Reponse To FireBase Server
                $ch = curl_init();
                curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                curl_setopt( $ch,CURLOPT_POST, true );
                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

                $result = curl_exec ( $ch );
                curl_close ( $ch );

                return response()->json(['status'=>1,'message'=>'Order has been placed'],200);
                
            } else {
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

                    if($request->address == ""){
                        return response()->json(["status"=>0,"message"=>"Address is required"],400);
                    }

                    if($request->lat == ""){
                        return response()->json(["status"=>0,"message"=>"Please select the address from suggestion"],400);
                    }

                    if($request->lang == ""){
                        return response()->json(["status"=>0,"message"=>"Please select the address from suggestion"],400);
                    }

                    if($request->pincode == ""){
                        return response()->json(["status"=>0,"message"=>"Pincode is required"],400);
                    }

                    if($request->building == ""){
                        return response()->json(["status"=>0,"message"=>"Door / Flat No. is required"],400);
                    }

                    if($request->landmark == ""){
                        return response()->json(["status"=>0,"message"=>"Landmark is required"],400);
                    }

                    $delivery_charge = $request->delivery_charge;
                    $address = $request->address;
                    $lat = $request->lat;
                    $lang = $request->lang;
                    $order_total = $request->order_total;
                    $building = $request->building;
                    $landmark = $request->landmark;
                    $postal_code = $request->pincode;
                }

                $order = new Order;
                $order->order_number =$order_number;
    	    	$order->user_id =$request->user_id;
    	    	$order->order_total =$order_total;
    	    	$order->payment_type =$request->payment_type;
                $order->order_type =$request->order_type;
                $order->status ='1';
    	    	$order->address =$address;
                $order->building =$building;
                $order->landmark =$landmark;
                $order->pincode =$postal_code;
                $order->lat =$lat;
                $order->lang =$lang;
                $order->promocode =$request->promocode;
                $order->discount_amount =$request->discount_amount;
                $order->discount_pr =$request->discount_pr;
                $order->tax =$request->tax;
                $order->tax_amount =$request->tax_amount;
                $order->delivery_charge =$delivery_charge;
                $order->order_notes =$request->order_notes;
                $order->order_from =$request->order_from;
    	    	$order->save();


    	    	$order_id = DB::getPdo()->lastInsertId();
    	    	$data=Cart::where('cart.user_id',$request['user_id'])
    	    	->get();
    	    	foreach ($data as $value) {
    	    	    $OrderPro = new OrderDetails;
    	    	    $OrderPro->order_id = $order_id;
                    $OrderPro->user_id = $value['user_id'];
    	    	    $OrderPro->item_id = $value['item_id'];
    	    	    $OrderPro->price = $value['price'];
    	    	    $OrderPro->qty = $value['qty'];
                    $OrderPro->item_notes = $value['item_notes'];
                    $OrderPro->addons_id = $value['addons_id'];
    	    	    $OrderPro->save();
                    
    	    	}
    	    	$cart=Cart::where('user_id', $request->user_id)->delete();

                //Notification
                $getalluses=User::select('token','email','name')->where('id',$request->user_id)
                ->get()->first();

                try{
                    $email=$getalluses->email;
                    $name=$getalluses->name;
                    $ordermessage='Order "'.$order_number.'" has been placed';
                    $data=['ordermessage'=>$ordermessage,'email'=>$email,'name'=>$name];

                    Mail::send('Email.orderemail',$data,function($message)use($data){
                        $message->from(env('MAIL_USERNAME'))->subject($data['ordermessage']);
                        $message->to($data['email']);
                    } );

                    $title = "Order";
                    $body = 'Order "'.$order_number.'" has been placed';
                    $google_api_key = $getdata->firebase; 
                    
                    $registrationIds = $getalluses->token;
                    #prep the bundle
                    $msg = array
                        (
                        'body'  => $body,
                        'title' => $title,
                        'sound' => 1/*Default sound*/
                        );
                    $fields = array
                        (
                        'to'            => $registrationIds,
                        'notification'  => $msg
                        );
                    $headers = array
                        (
                        'Authorization: key=' . $google_api_key,
                        'Content-Type: application/json'
                        );
                    #Send Reponse To FireBase Server
                    $ch = curl_init();
                    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                    curl_setopt( $ch,CURLOPT_POST, true );
                    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

                    $result = curl_exec ( $ch );
                    // dd($result);
                    curl_close ( $ch );
                }catch(\Swift_TransportException $e){
                    $response = $e->getMessage() ;
                    // return Redirect::back()->with('danger', $response);
                    return response()->json(['status'=>0,'message'=>'Something went wrong while sending email Please try again...'],200);
                }

                return response()->json(['status'=>1,'message'=>'Order has been placed'],200);
            }

    	} catch (\Exception $e){

    	    return response()->json(['status'=>0,'message'=>'Something went wrong'.$e],400);
    	}
    }

    public function orderhistory(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User ID is required"],400);
        }

        $cartdata=OrderDetails::select('order.order_total as total_price',DB::raw('SUM(order_details.qty) AS qty'),'order.id','order.order_type','order.order_number','order.status','order.payment_type',DB::raw('DATE_FORMAT(order.created_at, "%d-%m-%Y") as date'))
        ->join('item','order_details.item_id','=','item.id')
        ->join('order','order_details.order_id','=','order.id')
        ->where('order.user_id',$request->user_id)->groupBy('order_details.order_id')->orderBy('order_details.order_id', 'DESC')->get();

        if(!empty($cartdata))
        {
            return response()->json(['status'=>1,'message'=>'Order history list Successful','data'=>$cartdata],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'No data found'],200);
        }
    }

    public function getorderdetails(Request $request)
    {
        if($request->order_id == ""){
            return response()->json(["status"=>0,"message"=>"Order Number is required"],400);
        }

        $cartdata=OrderDetails::with('itemimage')->select('order_details.id','order_details.qty','order_details.price as total_price','order_details.item_notes','item.id','item.item_name','item.item_price','order_details.item_id','order_details.addons_id')
        ->join('item','order_details.item_id','=','item.id')
        ->join('order','order_details.order_id','=','order.id')
        ->where('order_details.order_id',$request->order_id)->get()->toArray();
        
        $status=Order::select('order.driver_id','order.address','order.building','order.landmark','order.pincode','order.promocode','order.discount_amount','order.order_number','order.status','order.order_notes','order.order_type','order.tax','order.delivery_charge')
        ->join('users','order.user_id','=','users.id')
        ->where('order.id',$request['order_id'])
        ->get()->first();

        $getdriver=User::select('users.name',\DB::raw("CONCAT('".url('/public/images/profile/')."/', users.profile_image) AS profile_image"),'users.mobile')->where('users.id',$status->driver_id)
        ->get()->first();

        foreach ($cartdata as $value) {
            $data[] = array(
                "total_price" => $value['total_price']
            );
        }

        foreach ($cartdata as $value) {

            $arr = explode(',', $value['addons_id']);
            $addons = Addons::whereIn('id',$arr)->get();

            // $images = ItemImages::where('id',$value['item_id'])->get();

            $cdata[] = array(
                "id" => $value['id'],
                "qty" => $value['qty'],
                "total_price" => $value['total_price'],
                "item_name" => $value['item_name'],
                "item_price" => $value['item_price'],
                "item_id" => $value['item_id'],
                "item_notes" => $value['item_notes'],
                "addons" => $addons,
                "itemimage" => $value["itemimage"]
            );
        }

        @$order_total = array_sum(array_column(@$data, 'total_price'));
        $summery = array(
            'order_total' => "$order_total",
            'tax' => $status->tax,
            'discount_amount' => $status->discount_amount,
            'promocode' => $status->promocode,
            'order_notes' => $status->order_notes,
            'delivery_charge' => $status->delivery_charge,
            "driver_name" => @$getdriver["name"],
            "driver_profile_image" => @$getdriver["profile_image"],
            "driver_mobile" => @$getdriver["mobile"],
        );
        
        if(!empty($cartdata))
        {
            return response()->json(['status'=>1,'message'=>'Summery list Successful','address'=>$status->address,'landmark' => $status->landmark,'building' => $status->building,'pincode'=>$status->pincode,'order_number'=>$status->order_number,'order_type'=>$status->order_type,'data'=>@$cdata,'summery'=>$summery],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'No data found'],200);
        }
    }

    public function ordercancel(Request $request)
    {
        if($request->order_id == ""){
            return response()->json(["status"=>0,"message"=>"Order Number is required"],400);
        }

        $status=Order::select('order.order_total','order.razorpay_payment_id','order.order_type','order.user_id','order.payment_type','order.user_id','order.order_total','order.order_number')
        ->join('users','order.user_id','=','users.id')
        ->where('order.id',$request['order_id'])
        ->get()->first();

        if ($status->payment_type != "0") {
            $walletdata=User::select('wallet')->where('id',$status->user_id)->first();

            $wallet = $walletdata->wallet + $status->order_total;

            $UpdateWalletDetails = User::where('id', $status->user_id)
            ->update(['wallet' => $wallet]);

            $Wallet = new Transaction;
            $Wallet->user_id = $status->user_id;
            $Wallet->order_id = $request->order_id;
            $Wallet->order_number = $status->order_number;
            $Wallet->wallet = $status->order_total;
            $Wallet->payment_id = $status->razorpay_payment_id;
            $Wallet->order_type = $status->order_type;
            $Wallet->transaction_type = '1';
            $Wallet->save();
        }

        $UpdateDetails = Order::where('id', $request->order_id)
                    ->update(['status' => '5']);
        
        if(!empty($UpdateDetails))
        {
            return response()->json(['status'=>1,'message'=>'Order has been cancelled'],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'Something went wrong'],400);
        }
    }

    public function wallet(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User ID is required"],400);
        }

        $walletamount=User::select('wallet')->where('id',$request->user_id)->first();

        // $walletamount=Transaction::select('wallet')->where('user_id',$request->user_id)->sum('wallet');

        $transaction_data=Transaction::select('order_number','transaction_type','wallet',DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y") as date'),'username')->where('user_id',$request->user_id)->orderBy('id', 'DESC')->get();

        if(!empty($transaction_data))
        {
            return response()->json(['status'=>1,'message'=>'Transaction list Successful','walletamount'=>number_format($walletamount->wallet ,2),'data'=>$transaction_data],200);
        }   
        else
        {
            return response()->json(['status'=>0,'message'=>'No data found'],200);
        }
    }

    public function paymenttype(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User ID is required"],400);
        }

        $getdata=Payment::select('payment_name','test_public_key','live_public_key','environment')->where('is_available','1')->orderBy('id', 'DESC')->get();

        // $walletamount=Transaction::select('wallet')->where('user_id',$request->user_id)->sum('wallet');

        $walletamount=User::select('wallet')->where('id',$request->user_id)->first();

        if(!empty($getdata))
        {
            return response()->json(['status'=>1,'message'=>'Transaction list Successful','walletamount'=>number_format($walletamount->wallet ,2),'payment'=>$getdata],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'No data found'],200);
        }
    }

    public function promocodelist()
    {
        
        $promocode=Promocode::select('promocode.offer_name','promocode.offer_code','promocode.offer_amount','promocode.description')
        ->where('is_available','=','1')
        ->get();

        if(!empty($promocode))
        {
            return response()->json(['status'=>1,'message'=>'Promocode List','data'=>$promocode],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'No Promocode found'],200);
        }
    }

    public function promocode(Request $request)
    {
        if($request->offer_code == ""){
            return response()->json(["status"=>0,"message"=>"Promocode is required"],400);
        }

        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"user_id is required"],400);
        }

        $checkpromo=Order::select('promocode')->where('promocode',$request->offer_code)->where('user_id',$request->user_id)
        ->count();

        if ($checkpromo > "0" ) {
            return response()->json(['status'=>0,'message'=>'The Offer Is Applicable Only Once Per User'],200);
        } else {
            $promocode=Promocode::select('promocode.offer_amount','promocode.description','promocode.offer_code')->where('promocode.offer_code',$request['offer_code'])
            ->get()->first();

            if($promocode['offer_code']== $request->offer_code) {
                if(!empty($promocode))
                {
                    return response()->json(['status'=>1,'message'=>'Promocode has been applied','data'=>$promocode],200);
                }
            } else {
                return response()->json(['status'=>0,'message'=>'You applied wrong Promocode'],200);
            }
        }
    }

    public function checkpincode(Request $request)
    {
        if($request->pincode == ""){
            return response()->json(["status"=>0,"message"=>"Pincode is required"],400);
        }

        $pincode=Pincode::select('pincode.pincode')->where('pincode.pincode',$request['pincode'])
        ->get()->first();

        if(@$pincode['pincode'] == $request->pincode) {
            if(!empty($pincode))
            {
                return response()->json(['status'=>1,'message'=>'Pincode is available for delivery'],200);
            }
        } else {
            return response()->json(['status'=>0,'message'=>'Delivery is not available in your area'],200);
        }
    }
}
