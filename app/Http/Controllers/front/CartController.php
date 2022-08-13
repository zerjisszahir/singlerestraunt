<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Cart;
use App\Addons;
use App\Promocode;
use App\User;
use App\About;
use App\Order;
use App\Item;
use Session;
use App\Time;
use App\Payment;
use DateTime;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $user_id  = Session::get('id');
        $getabout = About::where('id','=','1')->first();
        $cartdata=Cart::with('itemimage')->select('cart.id','cart.qty','cart.price','cart.item_notes','item.item_name','cart.item_id','cart.addons_id')
        ->join('item','cart.item_id','=','item.id')
        ->where('cart.user_id',$user_id)
        ->where('cart.is_available','=','1')
        ->orderby('id','desc')->get();

        foreach ($cartdata as $value) {
           $arr = explode(',', $value['addons_id']);
           $value['addons']=Addons::whereIn('id',$arr)->get();
        };

        $getpromocode=Promocode::select('promocode.offer_name','promocode.offer_code','promocode.offer_amount','promocode.description')
        ->where('is_available','=','1')
        ->get();

        $userinfo=User::select('name','email','mobile','wallet')->where('id',$user_id)
        ->get()->first();

        $taxval=User::select('tax','delivery_charge','currency','map')->where('type','1')
        ->get()->first();

        $getdata=User::select('max_order_qty','min_order_amount','max_order_amount')->where('type','1')
        ->get()->first();

        $getpaymentdata=Payment::select('payment_name','test_public_key','live_public_key','environment')->where('is_available','1')->orderBy('id', 'DESC')->get();

        return view('front.cart', compact('cartdata','getabout','getpromocode','taxval','userinfo','getdata','getpaymentdata'));
    }

    public function applypromocode(Request $request)
    {
        if($request->promocode == ""){
            return response()->json(["status"=>0,"message"=>"Promocode is required"],200);
        }

        $user_id  = Session::get('id');

        $checkpromo=Order::select('promocode')->where('promocode',$request->promocode)->where('user_id',$user_id)
        ->count();

        if ($checkpromo > "0" ) {
            return response()->json(['status'=>0,'message'=>'The Offer Is Applicable Only Once Per User'],200);
        } else {
            $promocode=Promocode::select('promocode.offer_amount','promocode.description','promocode.offer_code')->where('promocode.offer_code',$request['promocode'])
            ->get()->first();

                session ( [ 
                    'offer_amount' => $promocode->offer_amount, 
                    'offer_code' => $promocode->offer_code,
                ] );

            if($promocode['offer_code']== $request->promocode) {
                if(!empty($promocode))
                {
                    return response()->json(['status'=>1,'message'=>'Promocode has been applied','data'=>$promocode],200);
                }
            } else {
                return response()->json(['status'=>0,'message'=>'You applied wrong Promocode'],200);
            }
        }
    }

    public function removepromocode(Request $request)
    {
        
        $remove = session()->forget(['offer_amount','offer_code']);

        if(!$remove) {
            return response()->json(['status'=>1,'message'=>'Promo Code has been removed'],200);
        } else {
            return response()->json(['status'=>0,'message'=>'Something went wrong.'],200);
        }
    }

    public function qtyupdate(Request $request)
    {
        if($request->cart_id == ""){
            return response()->json(["status"=>0,"message"=>"Cart ID is required"],400);
        }
        if($request->item_id == ""){
            return response()->json(["status"=>0,"message"=>"Item is required"],400);
        }
        if($request->qty == ""){
            return response()->json(["status"=>0,"message"=>"Qty is required"],400);
        }

        $data=Item::where('item.id', $request['item_id'])
        ->get()
        ->first();

        $cartdata=Cart::where('cart.id', $request['cart_id'])
        ->get()
        ->first();

        $getdata=User::select('max_order_qty','min_order_amount','max_order_amount')->where('type','1')
        ->get()->first();

        if ($getdata->max_order_qty < $request->qty) {
          return response()->json(['status'=>0,'message'=>"You've reached the maximum units allowed for the purchase of this item"],200);
        }

        $arr = explode(',', $cartdata->addons_id);
        $d = Addons::whereIn('id',$arr)->get();

        $sum = 0;
        foreach($d as $key => $value) {
            $sum += $value->price; 
        }

        if ($request->type == "decreaseValue") {
            $qty = $cartdata->qty-1;
        } else {
            $qty = $cartdata->qty+1;
        }

        $update=Cart::where('id',$request['cart_id'])->update(['item_id'=>$request->item_id,'qty'=>$qty,'price'=>($qty)*($data->item_price+$sum)]);

        return response()->json(['status'=>1,'message'=>'Qty has been update'],200);
    }

    public function deletecartitem(Request $request)
    {
        if($request->cart_id == ""){
            return response()->json(["status"=>0,"message"=>"Cart data is required"],400);
        }

        $cart=Cart::where('id', $request->cart_id)->delete();

        $count=Cart::where('user_id',Session::get('id'))->count();
        
        Session::put('cart', $count);
        if($cart)
        {
            return 1;
        }
        else
        {
            return 2;
        }
    }

    public function isopenclose(Request $request)
    {
        $getdata=User::select('timezone')->where('type','1')->first();
        date_default_timezone_set($getdata->timezone);

        $date = date('Y/m/d h:i:sa');
        $day = date('l', strtotime($date));

        $isopenclose=Time::where('day','=',$day)->first();

        $current_time = DateTime::createFromFormat('H:i a', date("h:i a"));
        $open_time = DateTime::createFromFormat('H:i a', $isopenclose->open_time);
        $close_time = DateTime::createFromFormat('H:i a', $isopenclose->close_time);

        if ($current_time > $open_time && $current_time < $close_time && $isopenclose->always_close == "2") {
           return response()->json(['status'=>1,'message'=>'The restaurant is open now.'],200);
        } else {
           return response()->json(['status'=>0,'message'=>'Restaurant is closed.'],200);
        }
    }
}
