<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Banner;
use Validator;

class BannerController extends Controller
{
    public function banner(Request $request)
    {
    	$categorydata=Banner::select('id','item_id',\DB::raw("CONCAT('".url('/public/images/banner/')."/', image) AS image"))->orderby('id','desc')->get();
        if(!empty($categorydata))
        {
        	return response()->json(['status'=>1,'message'=>'Banner Successful','data'=>$categorydata],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'No data found'],200);
        }
    }
}
