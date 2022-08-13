<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Item;
use App\Favorite;
use App\User;
use App\ItemImages;
use Illuminate\Support\Facades\DB;
use Validator;

class ItemController extends Controller
{
    public function item(Request $request)
    {
        if($request->cat_id == ""){
            return response()->json(["status"=>0,"message"=>"category is required"],400);
        }

        $getdata=User::select('max_order_qty','min_order_amount','max_order_amount','currency','firebase','map','referral_amount')->where('type','1')
            ->get()->first();

        if($request->user_id == ""){
            $user_id  = $request->user_id;
            $itemdata=Item::with('itemimage')->select('item.id','item.item_name','item.item_price',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
            ->leftJoin('favorite', function($query) use($user_id) {
                $query->on('favorite.item_id','=','item.id')
                ->where('favorite.user_id', '=', $user_id);
            })
            ->join('categories','item.cat_id','=','categories.id')
            ->where('item.item_status','1')
            ->where('item.cat_id',$request['cat_id'])->orderBy('item.id', 'DESC')->paginate(10);

            if(!empty($itemdata))
            {
                return response()->json(['status'=>1,'message'=>'Item Successful','data'=>$itemdata,'currency'=>$getdata->currency,'max_order_qty'=>$getdata->max_order_qty,'min_order_amount'=>$getdata->min_order_amount,'max_order_amount'=>$getdata->max_order_amount,'map'=>$getdata->map,'referral_amount'=>$getdata->referral_amount],200);
            }
            else
            {
                return response()->json(['status'=>0,'message'=>'No data found'],200);
            }
            return response()->json(["status"=>0,"message"=>"category is required"],400);
        } else {
            $checkuser=User::where('id',$request->user_id)->first();

            if($checkuser->is_available == '1') 
            {
                $user_id  = $request->user_id;
                $itemdata=Item::with('itemimage')->select('item.id','item.item_name','item.item_price',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
                ->leftJoin('favorite', function($query) use($user_id) {
                    $query->on('favorite.item_id','=','item.id')
                    ->where('favorite.user_id', '=', $user_id);
                })
                ->join('categories','item.cat_id','=','categories.id')
                ->where('item.item_status','1')
                ->where('item.cat_id',$request['cat_id'])->orderBy('item.id', 'DESC')->paginate(10);

                if(!empty($itemdata))
                {
                    return response()->json(['status'=>1,'message'=>'Item Successful','data'=>$itemdata,'currency'=>$getdata->currency,'max_order_qty'=>$getdata->max_order_qty,'min_order_amount'=>$getdata->min_order_amount,'max_order_amount'=>$getdata->max_order_amount,'map'=>$getdata->map,'referral_amount'=>$getdata->referral_amount],200);
                }
                else
                {
                    return response()->json(['status'=>0,'message'=>'No data found'],200);
                }
                return response()->json(["status"=>0,"message"=>"category is required"],400);
            } else {
                $status=2;
                $message='Your account has been blocked by Admin';
                return response()->json(['status'=>$status,'message'=>$message],422);
            }
        }
    }

    public function itemdetails(Request $request)
    {
        if($request->item_id == ""){
            return response()->json(["status"=>0,"message"=>"Item ID is required"],400);
        }

    	$itemdata=Item::with('itemimagedetails')->with(['ingredients','addons'])->select('item.id','item.item_name','item.item_description','item.item_price','item.delivery_time','item.item_status','categories.category_name')
    	->join('categories','item.cat_id','=','categories.id')
    	->where('item.id',$request['item_id'])->get()->first();

        $data = array(
            'id' => $itemdata->id,
            'item_name' => $itemdata->item_name,
            'item_description' => $itemdata->item_description,
            'item_price' => $itemdata->item_price,
            'delivery_time' => $itemdata->delivery_time,
            'item_status' => $itemdata->item_status,
            'category_name' => $itemdata->category_name,
            // 'average_rating' => number_format($review->average_rating,1),
            'images' => $itemdata->itemimagedetails,
            'ingredients' => $itemdata->ingredients,
            'addons' => $itemdata->addons,
            
        ); 

        if(!empty($data))
        {
        	return response()->json(['status'=>1,'message'=>'Item Successful','data'=>$data],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'No data found'],200);
        }
    }

    public function searchitem(Request $request)
    {

        if($request->keyword == ""){
            return response()->json(["status"=>0,"message"=>"Keyword is required"],400);
        }

        $user_id  = $request->user_id;
        $itemdata=Item::with('itemimage')->select('item.id','item.item_name','item.item_price',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
        ->leftJoin('favorite', function($query) use($user_id) {
            $query->on('favorite.item_id','=','item.id')
            ->where('favorite.user_id', '=', $user_id);
        })
        ->join('categories','item.cat_id','=','categories.id')
        ->where('item.item_status','1')
        ->where('item.item_name', 'LIKE', '%' . $request['keyword'] . '%')->orderBy('item.id', 'DESC')->paginate(10);
        
        if(!$itemdata->isEmpty())
        {
            return response()->json(['status'=>1,'message'=>'Item Successful','data'=>$itemdata],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'No data found'],200);
        }
    }

    public function addfavorite(Request $request)
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

            if($data=="") {
                $favorite = new Favorite;
                $favorite->user_id =$request->user_id;
                $favorite->item_id =$request->item_id;
                $favorite->save();

                return response()->json(['status'=>1,'message'=>'Item has been added in Favorite list'],200);
            } else {
                return response()->json(['status'=>0,'message'=>'Item already in favorite list.'],400);
            }
            
        } catch (\Exception $e){
            return response()->json(['status'=>0,'message'=>'Something went wrong'],400);
        }
    }

    public function favoritelist(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User is required"],400);
        }

        $favorite=Favorite::with('itemimage')->select('favorite.id as favorite_id','item.id','item.item_name','item.item_price')
        ->join('item','favorite.item_id','=','item.id')
        ->where('item.item_status','1')
        ->where('favorite.user_id',$request['user_id'])->paginate(10); 

        if(!empty($favorite))
        {
            return response()->json(['status'=>1,'message'=>'Favorite List','data'=>$favorite],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'No data found'],200);
        }
    }

    public function removefavorite(Request $request)
    {
        if($request->favorite_id == ""){
            return response()->json(["status"=>0,"message"=>"Favorite product ID is required"],400);
        }

        $favorite=Favorite::where('id', $request->favorite_id)->delete();

        if($favorite)
        {
            return response()->json(['status'=>1,'message'=>'Item has been removed from favorite list'],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'No data found'],200);
        }
    }
}