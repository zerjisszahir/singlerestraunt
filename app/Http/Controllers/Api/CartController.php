<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Cart;
use App\Item;
use App\Addons;
use App\ItemImages;
use App\User;
use Illuminate\Support\Facades\DB;
use Validator;

class CartController extends Controller
{
    public function cart(Request $request)
    {
      if($request->item_id == ""){
          return response()->json(["status"=>0,"message"=>"Item is required"],400);
      }
      if($request->qty == ""){
          return response()->json(["status"=>0,"message"=>"Qty is required"],400);
      }
      if($request->price == ""){
          return response()->json(["status"=>0,"message"=>"Price is required"],400);
      }
      if($request->user_id == ""){
          return response()->json(["status"=>0,"message"=>"User ID is required"],400);
      }

      $data=Cart::where('cart.user_id',$request['user_id'])
              ->where('cart.item_id', $request['item_id'])
              ->where('cart.addons_id', $request['addons_id'])
              ->get()
              ->first();

      $getdata=User::select('max_order_qty','min_order_amount','max_order_amount')->where('type','1')
      ->get()->first();

  		try {
  		    if($data!="") {

              if (@$data->addons_id == $request['addons_id']) {

              	if ($request['qty'] == "") {
              		$qty = $data->qty+'1';
              	} else {
              		$qty = $data->qty+$request['qty'];
              	}

              	if ($request['qty'] == "") {
              		$price = $request->price*($data->qty+'1');
              	} else {
              		$price = $request->price+$data->price;
              	}

                if ($getdata->max_order_qty < $qty) {
                  return response()->json(['status'=>1,'message'=>"You've reached the maximum units allowed for the purchase of this item"],200);
                }

                $result = DB::table('cart')
                ->where('cart.user_id',$data['user_id'])
                ->where('cart.item_id', $data['item_id'])
                ->where('cart.addons_id', $data['addons_id'])
                ->where('cart.id', $data['id'])
                ->update([
                    'qty' => $qty,
                    'price' => $price,
                    'item_notes' => $request->item_notes,
                ]);
                return response()->json(['status'=>1,'message'=>'Qty has been update'],200);

              } elseif (@$data->addons_id == "" && $request['addons_id'] == "") {
              	if ($request['qty'] == "") {
              		$qty = $data->qty+'1';
              	} else {
              		$qty = $data->qty+$request['qty'];
              	}

              	if ($request['qty'] == "") {
              		$price = $request->price*($data->qty+'1');
              	} else {
              		$price = $request->price+$data->price;
              	}

                if ($getdata->max_order_qty < $qty) {
                  return response()->json(['status'=>1,'message'=>"You've reached the maximum units allowed for the purchase of this item"],200);
                }

                $result = DB::table('cart')
                ->where('cart.user_id',$data['user_id'])
                ->where('cart.item_id', $data['item_id'])
                ->where('cart.id', $data['id'])
                ->update([
                    'qty' => $qty,
                    'price' => $price,
                ]);
                return response()->json(['status'=>1,'message'=>'Qty has been update'],200);

              }
  	        } else {
  	            $cart = new Cart;
  	            $cart->item_id =$request->item_id;
                $cart->addons_id =$request->addons_id;
  	            $cart->qty =$request->qty;
  	            $cart->price =$request->price;
  	            $cart->user_id =$request->user_id;
                $cart->item_notes =$request->item_notes;
  	            $cart->save();

  	            return response()->json(['status'=>1,'message'=>'Item has been added to your cart'],200);
  	        }

  		} catch (\Exception $e){

  		    return response()->json(['status'=>0,'message'=>'Something went wrong'],400);
  		}
   	}

   	public function getcart(Request $request)
   	{
   	    if($request->user_id == ""){
   	        return response()->json(["status"=>0,"message"=>"User ID is required"],400);
   	    }

   	    $cartdata=Cart::with('itemimage')->select('cart.id','cart.qty','cart.price','cart.item_notes','item.item_name','cart.item_id','cart.addons_id')
   	    ->join('item','cart.item_id','=','item.id')
        ->where('cart.is_available','1')
   	    ->where('cart.user_id',$request->user_id)->get();

        foreach ($cartdata as $value) {
          $arr = explode(',', $value['addons_id']);
          $value['addons']=Addons::whereIn('id',$arr)->get();
        }

   	    if(!empty($cartdata))
   	    {
   	        return response()->json(['status'=>1,'message'=>'Cart Data Successful','data'=>$cartdata],200);
   	    }
   	    else
   	    {
   	        return response()->json(['status'=>0,'message'=>'No data found'],200);
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
   	    if($request->user_id == ""){   	        
          return response()->json(["status"=>0,"message"=>"User ID is required"],400);
   	    }

        $data=Item::where('item.id', $request['item_id'])
        ->get()
        ->first();
        $cartdata=Cart::where('cart.id', $request['cart_id'])
        ->get()
        ->first();

        $arr = explode(',', $cartdata->addons_id);
        $d = Addons::whereIn('id',$arr)->get();

        $sum = 0;
        foreach($d as $key => $value) {
           $sum += $value->price; 
        }

        $cart = new Cart;
        $cart->exists = true;
        $cart->id = $request->cart_id;
        $cart->item_id =$request->item_id;
  	    $cart->qty =$request->qty;
  	    $cart->price = $request->qty*($data->item_price+$sum);
  	    $cart->user_id =$request->user_id;
        $cart->save();

        return response()->json(['status'=>1,'message'=>'Qty has been update'],200);
   	}

    public function deletecartitem(Request $request)
    {
        if($request->cart_id == ""){
            return response()->json(["status"=>0,"message"=>"Cart data is required"],400);
        }

        $cart=Cart::where('id', $request->cart_id)->delete();
        if($cart)
        {
            return response()->json(['status'=>1,'message'=>'Item has been deleted'],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'Somethig went wrong'],200);
        }
    }

    public function cartcount(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User ID is required"],400);
        }

        $cartdata=Cart::where('user_id',$request->user_id)->where('is_available','1')->count();

        if(!empty($cartdata))
        {
            return response()->json(['status'=>1,'message'=>'Cart Data Successful','cart'=>$cartdata],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'No data found'],200);
        }
    }
}