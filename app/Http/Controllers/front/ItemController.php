<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Category;
use App\Item;
use App\ItemImages;
use App\Ingredients;
use App\Favorite;
use App\Cart;
use App\About;
use App\User;
use App\Addons;
use Session;

class ItemController extends Controller
{
    /**3
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $getabout = About::where('id','=','1')->first();
        $user_id  = Session::get('id');
        $getitem = Item::with(['category','itemimage'])->select('item.cat_id','item.id','item.item_name','item.item_price','item.item_description',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
        ->leftJoin('favorite', function($query) use($user_id) {
            $query->on('favorite.item_id','=','item.id')
            ->where('favorite.user_id', '=', $user_id);
        })
        ->where('item.item_status','1')->where('item.is_deleted','2')
        ->where('cat_id','=','1')->orderBy('id', 'DESC')->paginate(9);

        $getdata=User::select('currency')->where('type','1')->first();

        if(empty($getitem)){ 
            abort(404); 
        } else {
            return view('front.product',compact('getcategory','getabout','getitem','getdata'));   
        }
    }

    public function productdetails(Request $request) {
        $user_id  = Session::get('id');
        $getabout = About::where('id','=','1')->first();
        
        $getitem = Item::with('category')->select('item.*',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
        ->leftJoin('favorite', function($query) use($user_id) {
            $query->on('favorite.item_id','=','item.id')
            ->where('favorite.user_id', '=', $user_id);
        })->where('item.id','=',$request->id)->where('item.item_status','1')->where('item.is_deleted','2')->first();

        if(empty($getitem)){ 
            abort(404); 
        } else {
            $getimages = ItemImages::select(\DB::raw("CONCAT('".url('/public/images/item/')."/', image) AS image"))->where('item_id','=',$request->id)->get();

            $getingredients = Ingredients::select(\DB::raw("CONCAT('".url('/public/images/ingredients/')."/', image) AS image"))->where('item_id','=',$request->id)->get();

            $freeaddons = Addons::select('id','name','price')->where('item_id','=',$request->id)->where('is_available','=','1')->where('is_deleted','=','2')->where('price','=','0')->get();
            $paidaddons = Addons::select('id','name','price')->where('item_id','=',$request->id)->where('is_available','=','1')->where('is_deleted','=','2')->where('price','!=',"0")->get();

            $getcategory = Item::where('id','=',$request->id)->first();

            $user_id  = Session::get('id');
            $relatedproduct = Item::with(['category','itemimage'])->select('item.cat_id','item.id','item.item_name','item.item_price','item.item_description',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
            ->leftJoin('favorite', function($query) use($user_id) {
                $query->on('favorite.item_id','=','item.id')
                ->where('favorite.user_id', '=', $user_id);
            })
            ->where('item.item_status','1')->where('item.is_deleted','2')
            ->where('cat_id','=',$getcategory->cat_id)->where('item.id','!=',$request->id)->orderBy('id', 'DESC')->get();
        }

        $getdata=User::select('currency')->where('type','1')->first();

        return view('front.product-details', compact('getitem','getabout','getimages','getingredients','freeaddons','paidaddons','relatedproduct','getdata'));
    }

    public function show(Request $request)
    {
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $getabout = About::where('id','=','1')->first();
        $user_id  = Session::get('id');
        $getitem = Item::with(['category','itemimage'])->select('item.cat_id','item.id','item.item_name','item.item_price','item.item_description',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
        ->leftJoin('favorite', function($query) use($user_id) {
            $query->on('favorite.item_id','=','item.id')
            ->where('favorite.user_id', '=', $user_id);
        })
        ->where('item.item_status','1')->where('item.is_deleted','2')
        ->where('cat_id','=',$request->id)->orderBy('id', 'DESC')->paginate(9);

        $getdata=User::select('currency')->where('type','1')->first();
        return view('front.product', compact('getcategory','getitem','getabout','getdata'));
    }

    public function favorite(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User is required"],400);
        }
        if($request->item_id == ""){
            return response()->json(["status"=>0,"message"=>"Item is required"],400);
        }

        $data=Favorite::where([
            ['favorite.user_id',$request['user_id']],
            ['favorite.item_id',$request['item_id']]
        ])
        ->get()
        ->first();
        try {
            if ($data=="") {
                $favorite = new Favorite;
                $favorite->user_id =$request->user_id;
                $favorite->item_id =$request->item_id;
                $favorite->save();
                return 1;
            } else {
                return 0;
            }            
        } catch (\Exception $e){
            return response()->json(['status'=>0,'message'=>'Something went wrong'],200);
        }
    }

    public function unfavorite(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User is required"],400);
        }
        if($request->item_id == ""){
            return response()->json(["status"=>0,"message"=>"Item is required"],400);
        }

        $unfavorite=Favorite::where('user_id', $request->user_id)->where('item_id', $request->item_id)->delete();
        if ($unfavorite) {
            return 1;
        } else {
            return 0;
        }
    }

    public function addtocart(Request $request)
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
                      return response()->json(['status'=>0,'message'=>"You've reached the maximum units allowed for the purchase of this item"],200);
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
                      return response()->json(['status'=>0,'message'=>"You've reached the maximum units allowed for the purchase of this item"],200);
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

                    $count=Cart::where('user_id',$request->user_id)->count();

                    Session::put('cart', $count);
                    return response()->json(['status'=>1,'message'=>'Item has been added to your cart','cartcnt'=>$count],200);
                }

            } catch (\Exception $e){

                return response()->json(['status'=>0,'message'=>'Something went wrong'],400);
            }
    }

    public function search(Request $request)
    {
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $getabout = About::where('id','=','1')->first();
        $user_id  = Session::get('id');
        $getitem = Item::with(['category','itemimage'])->select('item.cat_id','item.id','item.item_name','item.item_price','item.item_description',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
        ->leftJoin('favorite', function($query) use($user_id) {
            $query->on('favorite.item_id','=','item.id')
            ->where('favorite.user_id', '=', $user_id);
        })->where('item.item_name','LIKE','%' . $request->item . '%')->where('item.item_status','1')->where('item.is_deleted','2')->orderBy('id', 'DESC')->paginate(9);

        $getdata=User::select('currency')->where('type','1')->first();
        return view('front.search', compact('getcategory','getabout','getitem','getdata'));
    }
}
