<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\PrivacyPolicy;
use App\Category;
use App\User;
use App\Pincode;
use App\Item;
use App\Addons;
use App\Ratting;
use App\Banner;
use App\Order;
use App\Promocode;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function policy() {
        $getprivacypolicy = PrivacyPolicy::where('id', '1')->first();
        return view('privacy-policy', compact('getprivacypolicy'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $getcategory = Category::where('is_available','1')->where('is_deleted','2')->get();
        $getitems = Item::where('item_status','1')->where('is_deleted','2')->get();
        $getpincode = Pincode::all();
        $addons = Addons::where('is_available','1')->where('is_deleted','2')->get();
        $getreview = Ratting::all();
        $getorders = Order::all();
        $order_total = Order::where('status','!=','5')->where('status','!=','6')->sum('order_total');
        $order_tax = Order::where('status','!=','5')->where('status','!=','6')->sum('tax_amount');
        $getpromocode = Promocode::all();
        $getusers = User::Where('type', '=' , '2')->get();
        $driver = User::Where('type', '=' , '3')->get();
        $banners = Banner::all();
        $getdriver = User::where('type','3')->get();
        $todayorders = Order::with('users')->select('order.*','users.name')->leftJoin('users', 'order.driver_id', '=', 'users.id')->where('order.created_at','LIKE','%' .date("Y-m-d") . '%')->get();
        return view('home',compact('getcategory','getpincode','getitems','addons','getusers','driver','banners','getreview','getorders','order_total','order_tax','getpromocode','todayorders','getdriver'));
    }

    public function auth(Request $request)
    {
        $username = str_replace(' ','',$request->envato_username);

        $payload = file_get_contents('https://gravityinfotech.net/api/getdata.php?envato_username='.$username.'&email='.$request->email.'&purchase_key='.$request->purchase_key.'&domain='.$request->domain.'');
        $obj = json_decode($payload);

        if ($obj->status == '1') {
            $users = User::where('type', '1')->update( array('purchase_key'=>$request->purchase_key) );
            return Redirect::to('/admin')->with('success', 'You have successfully verified your License. Please try to Login now. If any query Contact us xvirus.bd@gmail.com');
        } else {
            return Redirect::back()->with('danger', $obj->message);
        }
    }
}
