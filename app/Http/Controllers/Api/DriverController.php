<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Order;
use App\Addons;
use App\OrderDetails;
use Validator;

class DriverController extends Controller
{
    public function login(Request $request )
    {
        if($request->email == ""){
            return response()->json(["status"=>0,"message"=>"Email id is required"],400);
        }
        if($request->password == ""){
            return response()->json(["status"=>0,"message"=>"Password is required"],400);
        }
        
        $login=User::where('email',$request['email'])->where('type','=','3')->first();

        if(!empty($login))
        {
            if($login->is_available == '1') 
            {
                if(Hash::check($request->get('password'),$login->password))
                {
                    $arrayName = array(
                        'id' => $login->id,
                        'name' => $login->name,
                        'mobile' => $login->mobile,
                        'email' => $login->email,
                    );
                    // $login->fcm_token = '';
                    $data=array('user'=>$arrayName);
                    $status=1;
                    $message='Login Successful';

                    $data_token['token'] = $request['token'];
                    $update=User::where('email',$request['email'])->update($data_token);

                    return response()->json(['status'=>$status,'message'=>$message,'data'=>$arrayName],200);
                }
                else
                {
                    $status=0;
                    $message='Password is incorrect';
                    return response()->json(['status'=>$status,'message'=>$message],422);
                }
            }
            else
            {
                $status=0;
                $message='Your account has been blocked by Admin';
                return response()->json(['status'=>$status,'message'=>$message],422);
            }
        }
        else
        {
            $status=0;
            $message='Email is incorrect';
            $data="";
            return response()->json(['status'=>$status,'message'=>$message],422);
        }
        
       
        return response()->json(['status'=>$status,'message'=>$message,'data'=>$data],200);
    }

    public function getprofile(Request $request )
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User ID is required"],400);
        }

        $users = User::where('id',$request['user_id'])->where('type','=','3')->get()->first();

        if ($users->mobile == "") {
            $mobile = "";
        } else {
            $mobile = $users->mobile;
        }

        $arrayName = array(
            'id' => $users->id,
            'name' => $users->name,
            'mobile' => $mobile,
            'email' => $users->email,
            'profile_image' => url('/public/images/profile/'.$users->profile_image)
        );


        if(!empty($arrayName))
        {
            return response()->json(['status'=>1,'message'=>'Profile data','data'=>$arrayName],200);
        } else {
            $status=0;
            $message='No User found';
            $data="";
            return response()->json(['status'=>$status,'message'=>$message],422);
        }

        return response()->json(['status'=>$status,'message'=>$message,'data'=>$data],200);
    }

    public function editprofile(Request $request )
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User ID is required"],400);
        }
        if($request->name == ""){
            return response()->json(["status"=>0,"message"=>"Name is required"],400);
        }

        $user = new User;
        $user->exists = true;
        $user->id = $request->user_id;

        if(isset($request->image)){
            if($request->hasFile('image')){
                $image = $request->file('image');
                $image = 'profile-' . uniqid() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move('public/images/profile', $image);
                $user->profile_image=$image;
            }            
        }
        $user->name =$request->name;
        $user->save();

        if($user)
        {
            return response()->json(['status'=>1,'message'=>'Profile has been updated'],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'Something went wrong'],400);
        }
    }

    public function changepassword(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User is required"],400);
        }
        if($request->old_password == ""){
            return response()->json(["status"=>0,"message"=>"Old Password is required"],400);
        }
        if($request->new_password == ""){
            return response()->json(["status"=>0,"message"=>"New Password is required"],400);
        }
        if($request['old_password']==$request['new_password'])
        {
            return response()->json(['status'=>0,'message'=>'Old and new password must be different'],400);
        }
        $check_user=User::where('id',$request['user_id'])->where('type','=','3')->get()->first();
        if(Hash::check($request['old_password'],@$check_user->password))
        {
            $data['password']=Hash::make($request['new_password']);
            $update=User::where('id',$request['user_id'])->update($data);
            return response()->json(['status'=>1,'message'=>'Password Updated'],200);
        }
        else{
            return response()->json(['status'=>0,'message'=>'Incorrect Password'],400);
        }
    }

    public function forgotPassword(Request $request)
    {
        if($request->email == ""){
            return response()->json(["status"=>0,"message"=>"Email id is required"],400);
        }

        $checklogin=User::where('email',$request['email'])->where('type','=','3')->first();
        
        if(empty($checklogin))
        {
            return response()->json(['status'=>0,'message'=>'Email does not exist'],400);
        }
        else {
            try{
                $password = mt_rand(100000, 999999);
                $newpassword['password'] = Hash::make($password);
                $update = User::where('email', $request['email'])->update($newpassword);

                $title='Password Reset';
                $email=$checklogin->email;
                $name=$checklogin->name;
                $data=['title'=>$title,'email'=>$email,'name'=>$name,'password'=>$password];

                Mail::send('Email.email',$data,function($message)use($data){
                    $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                    $message->to($data['email']);
                } );
                return response()->json(['status'=>1,'message'=>'New Password Sent to your email address'],200);
            }catch(\Swift_TransportException $e){
                $response = $e->getMessage() ;
                return response()->json(['status'=>0,'message'=>'Something went wrong while sending email Please try again...'],200);
            }
        }

    }

    public function ongoingorder(Request $request)
    {
        if($request->driver_id == ""){
            return response()->json(["status"=>0,"message"=>"Driver ID is required"],400);
        }

        $getdata=User::select('currency')->where('type','1')->first();

        $checkuser=User::where('id',$request->driver_id)->first();

        if($checkuser->is_available == '1') 
        {

            $cartdata=OrderDetails::select('order.order_total as total_price',DB::raw('SUM(order_details.qty) AS qty'),'order.id','order.order_number','order.status','order.payment_type',DB::raw('DATE_FORMAT(order.created_at, "%d-%m-%Y") as date'))
            ->join('item','order_details.item_id','=','item.id')
            ->join('order','order_details.order_id','=','order.id')
            ->where('order.driver_id',$request->driver_id)->where('order.status','3')->groupBy('order_details.order_id')->orderBy('order.created_at','DESC')->get();

            $completed_order=Order::where('order.status','4')->where('order.driver_id',$request->driver_id)
            ->count();

            $ongoing_order=Order::where('order.status','3')->where('order.driver_id',$request->driver_id)
            ->count();

            if(!empty($cartdata))
            {
                return response()->json(['status'=>1,'message'=>'Order history list Successful','completed_order'=>$completed_order,'ongoing_order'=>$ongoing_order,'currency'=>$getdata->currency,'data'=>$cartdata],200);
            }
            else
            {
                return response()->json(['status'=>0,'message'=>'No data found'],200);
            }
        } else {
            $status=2;
            $message='Your account has been blocked by Admin';
            return response()->json(['status'=>$status,'message'=>$message],422);
        }
    }

    public function orderhistory(Request $request)
    {
        if($request->driver_id == ""){
            return response()->json(["status"=>0,"message"=>"Driver ID is required"],400);
        }

        $cartdata=OrderDetails::select('order.order_total as total_price',DB::raw('SUM(order_details.qty) AS qty'),'order.id','order.order_number','order.status','order.payment_type',DB::raw('DATE_FORMAT(order.created_at, "%d-%m-%Y") as date'))
        ->join('item','order_details.item_id','=','item.id')
        ->join('order','order_details.order_id','=','order.id')
        ->where('order.driver_id',$request->driver_id)->where('order.status','4')->groupBy('order_details.order_id')->orderBy('order.created_at','DESC')->get();


        $completed_order=Order::where('order.status','4')->where('order.driver_id',$request->driver_id)
        ->count();

        $ongoing_order=Order::where('order.status','3')->where('order.driver_id',$request->driver_id)
        ->count();

        if(!empty($cartdata))
        {
            return response()->json(['status'=>1,'message'=>'Order history list Successful','completed_order'=>$completed_order,'ongoing_order'=>$ongoing_order,'data'=>$cartdata],200);
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
        
        $status=Order::select('order.address','order.landmark','order.building','order.pincode','order.promocode','order.discount_amount','order.order_number','order.status','order.order_notes','order.delivery_charge','order.lat','order.lang','users.name',\DB::raw("CONCAT('".url('/public/images/profile/')."/', users.profile_image) AS profile_image"),'users.mobile')->where('order.id',$request['order_id'])
        ->join('users','order.user_id','=','users.id')
        ->get()->first();

        $taxval=User::select('users.tax')->where('users.id','1')
        ->get()->first();

        foreach ($cartdata as $value) {
            $arr = explode(',', $value['addons_id']);
            $addons = Addons::whereIn('id',$arr)->get();

            $data[] = array(
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
            'tax' => "$taxval->tax",
            'discount_amount' => $status->discount_amount,
            'promocode' => $status->promocode,
            'order_notes' => $status->order_notes,
            'delivery_charge' => "$status->delivery_charge",
        );
        
        if(!empty($cartdata))
        {
            return response()->json(['status'=>1,'message'=>'Summery list Successful','delivery_address'=>$status->address,'landmark'=>$status->landmark,'building'=>$status->building,'pincode'=>$status->pincode,'order_number'=>$status->order_number,'name'=>$status->name,'profile_image'=>$status->profile_image,'mobile'=>$status->mobile,'lat'=>$status->lat,'lang'=>$status->lang,'data'=>@$data,'summery'=>$summery],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'No data found'],200);
        }
    }

    public function delivered(Request $request)
    {
        if($request->order_id == ""){
            return response()->json(["status"=>0,"message"=>"Order Number is required"],400);
        }

        $UpdateDetails = Order::where('id', $request->order_id)
                    ->update(['status' => '4']);
        $getdata=User::select('firebase')->where('type','1')->first();

        if ($UpdateDetails) {

            //Notification

            $getuser = Order::where('id', $request->order_id)->first();
            
            $google_api_key = $getdata->firebase;

            $title = "Order";

            if ($getuser->driver_id) {

                $gettoken=User::select('token','name','email')->where('id',$getuser->driver_id)
                ->get()->first();

                $body = 'Order '.$getuser->order_number.' is Delivered';

                try{
                    $ordermessage='Order "'.$getuser->order_number.'" is Delivered';
                    $email=$gettoken->email;
                    $name=$gettoken->name;
                    $data=['ordermessage'=>$ordermessage,'email'=>$email,'name'=>$name];

                    Mail::send('Email.orderemail',$data,function($message)use($data){
                        $message->from(env('MAIL_USERNAME'))->subject($data['ordermessage']);
                        $message->to($data['email']);
                    } );
                }catch(\Swift_TransportException $e){
                    $response = $e->getMessage() ;
                    return response()->json(['status'=>0,'message'=>'Something went wrong while sending email Please try again...'],200);
                }

                $registrationIds = $gettoken->token;
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
            }

            if ($getuser->user_id) {
                $gettoken=User::select('token','name','email')->where('id',$getuser->user_id)
                ->get()->first();

                $body = 'Your Order '.$getuser->order_number.' is Delivered';

                try{
                    $ordermessage='Order "'.$getuser->order_number.'" is Delivered';
                    $email=$gettoken->email;
                    $name=$gettoken->name;
                    $data=['ordermessage'=>$ordermessage,'email'=>$email,'name'=>$name];

                    Mail::send('Email.orderemail',$data,function($message)use($data){
                        $message->from(env('MAIL_USERNAME'))->subject($data['ordermessage']);
                        $message->to($data['email']);
                    } );
                }catch(\Swift_TransportException $e){
                    $response = $e->getMessage() ;
                    return response()->json(['status'=>0,'message'=>'Something went wrong while sending email Please try again...'],200);
                }

                $registrationIds = $gettoken->token;
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
            }

            return response()->json(['status'=>1,'message'=>'Order has been delivered'],200);
        } else {
            return response()->json(['status'=>0,'message'=>'Something went wrong'],200);
        }
    }
}
