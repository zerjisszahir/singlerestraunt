<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Transaction;
use Validator;

class UserController extends Controller
{
    public function register(Request $request )
    {
        $checkemail=User::where('email',$request['email'])->first();
        $checkmobile=User::where('mobile',$request['mobile'])->first();

        $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; 
        $referral_code = substr(str_shuffle($str_result), 0, 10); 
        $otp = rand ( 100000 , 999999 );

        if ($request->register_type == "email") {
            if($request->email == ""){
                return response()->json(["status"=>0,"message"=>"Email ID is required"],400);
            }
            if($request->name == ""){
                return response()->json(["status"=>0,"message"=>"Name is required"],400);
            }
            if($request->mobile == ""){
                return response()->json(["status"=>0,"message"=>"Mobile is required"],400);
            }
            if($request->token == ""){
                return response()->json(["status"=>0,"message"=>"Token is required"],400);
            }

            if(!empty($checkemail))
            {
                return response()->json(['status'=>0,'message'=>'Email already exist in our system.'],400);
            }

            if(!empty($checkmobile))
            {
                return response()->json(['status'=>0,'message'=>'Mobile number already exist in our system.'],400);
            }

            if ($request->login_type == "google" OR $request->login_type == "facebook") {
                $password = "";
            } else {
                $password = Hash::make($request->get('password'));
            }

            $getdata=User::select('referral_amount','firebase','currency')->where('type','1')->get()->first();

            $checkreferral=User::select('id','name','referral_code','wallet','email','token')->where('referral_code',$request['referral_code'])->first();

            if (@$checkreferral->referral_code == $request['referral_code']) {
                                
                $title='Email Verification Code';
                $email=$request->email;
                $data=['title'=>$title,'email'=>$email,'otp'=>$otp];
                
                Mail::send('Email.emailverification',$data,function($message)use($data){
                    $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                    $message->to($data['email']);
                } );

                $data['name']=$request->get('name');
                $data['mobile']=$request->get('mobile');
                $data['email']=$request->get('email');
                $data['profile_image']='unknown.png';
                $data['password']=$password;
                $data['token'] = $request->get('token');
                $data['login_type']=$request->get('login_type');
                $data['google_id']=$request->get('google_id');
                $data['facebook_id']=$request->get('facebook_id');
                $data['referral_code']=$referral_code;
                $data['otp']=$otp;
                $data['type']='2';

                $user=User::create($data);

                $wallet = $checkreferral->wallet + $getdata->referral_amount;

                if ($request['referral_code'] != "") {
                   $wallet = $checkreferral->wallet + $getdata->referral_amount;

                   if ($wallet) {
                       $UpdateWalletDetails = User::where('id', $checkreferral->id)
                       ->update(['wallet' => $wallet]);

                       $from_Wallet = new Transaction;
                       $from_Wallet->user_id = $checkreferral->id;
                       $from_Wallet->order_id = null;
                       $from_Wallet->order_number = null;
                       $from_Wallet->wallet = $getdata->referral_amount;
                       $from_Wallet->payment_id = null;
                       $from_Wallet->order_type = '0';
                       $from_Wallet->transaction_type = '3';
                       $from_Wallet->username = $user->name;
                       $from_Wallet->save();

                       //Notification
                       try{
                           $email=$checkreferral->email;
                           $toname=$checkreferral->name;
                           $name=$user->name;
                           
                           $referralmessage='Your friend "'.$name.'" has used your referral code to register with Grocery User. You have earned "'.$getdata->currency.''.number_format($getdata->referral_amount,2).'" referral amount in your wallet.';
                           $data=['referralmessage'=>$referralmessage,'email'=>$email,'toname'=>$toname,'name'=>$name];

                           Mail::send('Email.referral',$data,function($message)use($data){
                               $message->from(env('MAIL_USERNAME'))->subject($data['referralmessage']);
                               $message->to($data['email']);
                           } );

                           $title = "Referral Earning";
                           $body = 'Your friend "'.$name.'" has used your referral code to register with Grocery User. You have earned "'.$getdata->currency.''.number_format($getdata->referral_amount,2).'" referral amount in your wallet.';
                           $google_api_key = $getdata->firebase; 
                           
                           $registrationIds = $checkreferral->token;
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
                   }

                   if ($getdata->referral_amount) {
                       $UpdateWallet = User::where('id', $user->id)
                       ->update(['wallet' => $getdata->referral_amount]);

                       $to_Wallet = new Transaction;
                       $to_Wallet->user_id = $user->id;
                       $to_Wallet->order_id = null;
                       $to_Wallet->order_number = null;
                       $to_Wallet->wallet = $getdata->referral_amount;
                       $to_Wallet->payment_id = null;
                       $to_Wallet->order_type = '0';
                       $to_Wallet->transaction_type = '3';
                       $to_Wallet->username = $checkreferral->name;
                       $to_Wallet->save();
                   }
                }
                

                if($user)
                {
                    $arrayName = array(
                        'id' => $user->id,
                        'name' => $user->name,
                        'mobile' => $user->mobile,
                        'email' => $user->email,
                        'referral_code' => $user->referral_code,
                        'profile_image' => url('/public/images/profile/'.$user->profile_image),
                    );
                    return response()->json(['status'=>1,'message'=>'Registration Successful','data'=>$arrayName,'otp'=>$otp],200);
                }
                else
                {
                    return response()->json(['status'=>0,'message'=>'Something went wrong'],400);
                }

            } else {
                return response()->json(['status'=>0,'message'=>'Referral code is invalid'],200);
            }
            
        }
        if ($request->login_type == "google") {
            if($request->email == ""){
                return response()->json(["status"=>0,"message"=>"Email ID is required"],400);
            }
            if($request->name == ""){
                return response()->json(["status"=>0,"message"=>"Name is required"],400);
            }
            if($request->token == ""){
                return response()->json(["status"=>0,"message"=>"Token is required"],400);
            }
            if($request->google_id == ""){
                return response()->json(["status"=>0,"message"=>"Google id is required"],400);
            }

            $usergoogle=User::where('google_id',$request->google_id)->first();
            if ($usergoogle != "" OR @$usergoogle->email == $request->email AND $request->email != "") {
                if ($usergoogle->mobile == "") {
                    $arrayName = array(
                        'id' => $usergoogle->id
                    );
                    return response()->json(['status'=>2,'message'=>"Please add your mobile number",'data'=>$arrayName],200);
                } else {
                    if($usergoogle->is_verified == '1') 
                    {
                        if($usergoogle->is_available == '1') 
                        {
                            $arrayName = array(
                                'id' => $usergoogle->id,
                                'name' => $usergoogle->name,
                                'mobile' => $usergoogle->mobile,
                                'email' => $usergoogle->email,
                                'referral_code' => $usergoogle->referral_code,
                                'profile_image' => url('/public/images/profile/'.$usergoogle->profile_image),
                            );

                            $update=User::where('email',$usergoogle['email'])->update(['token'=>$request->token]);
                            return response()->json(['status'=>1,'message'=>'Login Successful','data'=>$arrayName],200);
                        } else {
                            return response()->json(['status'=>0,'message'=>'Your account has been blocked by Admin'],200);
                        }
                    } else {
                                        
                        $title='Email Verification Code';
                        $email=$usergoogle->email;
                        $data=['title'=>$title,'email'=>$email,'otp'=>$otp];

                        Mail::send('Email.emailverification',$data,function($message)use($data){
                            $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                            $message->to($data['email']);
                        } );

                        $otp_data['otp'] = $otp;
                        $update=User::where('email',$usergoogle->email)->update($otp_data);

                        $status=3;
                        $message="You haven't verified your email address";
                        return response()->json(['status'=>$status,'message'=>$message,'otp'=>$otp],422);
                    }
                }
            } else {
                
                if(!empty($checkemail))
                {
                    return response()->json(['status'=>0,'message'=>'Email already exist in our system.'],400);
                }

                return response()->json(['status'=>2,'message'=>'Successful'],200);

            }
        } elseif ($request->login_type == "facebook") {
            if($request->email == ""){
                return response()->json(["status"=>0,"message"=>"Email ID is required"],400);
            }
            if($request->name == ""){
                return response()->json(["status"=>0,"message"=>"Name is required"],400);
            }
            if($request->token == ""){
                return response()->json(["status"=>0,"message"=>"Token is required"],400);
            }
            if($request->facebook_id == ""){
                return response()->json(["status"=>0,"message"=>"Facebook id is required"],400);
            }

            $userfacebook=User::where('users.facebook_id',$request->facebook_id)->first();

            if ($userfacebook != "" OR @$userfacebook->email == $request->email AND $request->email != "") {
                if ($userfacebook->mobile == "") {
                    $arrayName = array(
                        'id' => $userfacebook->id
                    );
                    return response()->json(['status'=>2,'message'=>"Please add your mobile number",'data'=>$arrayName],200);
                } else {
                    if($userfacebook->is_verified == '1') 
                    {
                        if($userfacebook->is_available == '1') 
                        {
                            $arrayName = array(
                                'id' => $userfacebook->id,
                                'name' => $userfacebook->name,
                                'mobile' => $userfacebook->mobile,
                                'email' => $userfacebook->email,
                                'referral_code' => $userfacebook->referral_code,
                                'profile_image' => url('/public/images/profile/'.$userfacebook->profile_image),
                            );
                            $update=User::where('email',$userfacebook['email'])->update(['token'=>$request->token]);
                            return response()->json(['status'=>1,'message'=>'Login Successful','data'=>$arrayName],200);
                        } else {
                            return response()->json(['status'=>0,'message'=>'Your account has been blocked by Admin'],200);
                        }
                        
                    } else {
                                        
                        $title='Email Verification Code';
                        $email=$userfacebook->email;
                        $data=['title'=>$title,'email'=>$email,'otp'=>$otp];

                        Mail::send('Email.emailverification',$data,function($message)use($data){
                            $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                            $message->to($data['email']);
                        } );

                        $otp_data['otp'] = $otp;
                        $update=User::where('email',$userfacebook->email)->update($otp_data);

                        $status=3;
                        $message="You haven't verified your email address";
                        return response()->json(['status'=>$status,'message'=>$message,'otp'=>$otp],422);
                    }
                }
            } else {
                
                if(!empty($checkemail))
                {
                    return response()->json(['status'=>0,'message'=>'Email already exist in our system.'],400);
                }

                return response()->json(['status'=>2,'message'=>'Successful'],200);

            }
        }
    }

    public function emailverify(Request $request )
    {
        if($request->email == ""){
            return response()->json(["status"=>0,"message"=>"Email is required"],400);
        }
        if($request->otp == ""){
            return response()->json(["status"=>0,"message"=>"OTP is required"],400);
        }
        if($request->token == ""){
            return response()->json(["status"=>0,"message"=>"Token is required"],400);
        }

        $checkuser=User::where('email',$request->email)->first();

        if (!empty($checkuser)) {
            if ($checkuser->otp == $request->otp) {
                $update=User::where('email',$request['email'])->update(['otp'=>NULL,'is_verified'=>'1','token'=>$request->token]);

                $arrayName = array(
                    'id' => $checkuser->id,
                    'name' => $checkuser->name,
                    'mobile' => $checkuser->mobile,
                    'email' => $checkuser->email,
                    'referral_code' => $checkuser->referral_code,
                    'profile_image' => url('/public/images/profile/'.$checkuser->profile_image),
                );

                return response()->json(['status'=>1,'message'=>"Email is verified",'data'=>$arrayName],200);

            } else {
                return response()->json(["status"=>0,"message"=>"Invalid OTP"],400);
            }  
        } else {
            return response()->json(["status"=>0,"message"=>"Email is invalid"],400);
        }  
    }

    public function resendemailverification(Request $request )
    {
        if($request->email == ""){
            return response()->json(["status"=>0,"message"=>"Email is required"],400);
        }

        $checkuser=User::where('email',$request->email)->first();

        if (!empty($checkuser)) {           

            try{
                $otp = rand ( 100000 , 999999 );

                $update=User::where('email',$request['email'])->update(['otp'=>$otp,'is_verified'=>'2']);

                $title='Email Verification Code';
                $email=$request->email;
                $data=['title'=>$title,'email'=>$email,'otp'=>$otp];

                Mail::send('Email.emailverification',$data,function($message)use($data){
                    $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                    $message->to($data['email']);
                } );
            }catch(\Swift_TransportException $e){
                $response = $e->getMessage() ;
                return response()->json(['status'=>0,'message'=>'Something went wrong while sending email. Please try again'],200);
            }

            return response()->json(["status"=>1,"message"=>"Email is sent to your registered email address",'otp'=>$otp],200);

        } else {
            return response()->json(["status"=>0,"message"=>"Email is invalid"],400);
        }  
    }

    public function login(Request $request )
    {
        if($request->email == ""){
            return response()->json(["status"=>0,"message"=>"Email id is required"],400);
        }
        if($request->password == ""){
            return response()->json(["status"=>0,"message"=>"Password is required"],400);
        }
        
        $login=User::where('email',$request['email'])->where('type','=','2')->first();

        if(!empty($login))
        {
            if($login->is_verified == '1') 
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
                            'referral_code' => $login->referral_code,
                            'profile_image' => url('/public/images/profile/'.$login->profile_image),
                        );

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
            } else {

                $otp = rand ( 100000 , 999999 );
                
                $title='Email Verification Code';
                $email=$request->email;
                $data=['title'=>$title,'email'=>$email,'otp'=>$otp];

                Mail::send('Email.emailverification',$data,function($message)use($data){
                    $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                    $message->to($data['email']);
                } );

                $otp_data['otp'] = $otp;
                $update=User::where('email',$request->email)->update($otp_data);

                $status=2;
                $message="You haven't verified your email address";
                return response()->json(['status'=>$status,'message'=>$message,'otp'=>$otp],422);
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

    public function AddMobile(Request $request)
    {
        if($request->mobile == ""){
            return response()->json(["status"=>0,"message"=>"Mobile is required"],400);
        }
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User ID is required"],400);
        }

        $checkmobile=User::where('mobile',$request['mobile'])->first();
        
        if(!empty($checkmobile))
        {
            return response()->json(['status'=>0,'message'=>'Mobile number already exist in our system.'],400);
        }

        try {
            $update=User::where('id',$request['user_id'])->update($data);
            return response()->json(["status"=>1,"message"=>"Mobile number has been updated"],200);

        } catch (\Exception $e){
            return response()->json(['status'=>0,'message'=>'Something went wrong'],400);
        }
    }

    public function getprofile(Request $request )
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User ID is required"],400);
        }

        $users = User::where('id',$request['user_id'])->get()->first();

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
            return response()->json(['status'=>0,'message'=>"No User found"],422);
        }

        return response()->json(['status'=>0,'message'=>'Something went wrong'],400);
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

        $user_details=User::where('id',$request->user_id)->where('type','=','2')->first();
        $arrayName = array(
            'id' => $user_details->id,
            'name' => $user_details->name,
            'mobile' => $user_details->mobile,
            'email' => $user_details->email,
            'profile_image' => url('/public/images/profile/'.$user_details->profile_image),
        );

        if($user)
        {
            return response()->json(['status'=>1,'message'=>'Profile has been updated','data'=>$arrayName],200);
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
        $check_user=User::where('id',$request['user_id'])->get()->first();
        if(Hash::check($request['old_password'],$check_user->password))
        {
            $data['password']=Hash::make($request['new_password']);
            $update=User::where('id',$request['user_id'])->update($data);
            return response()->json(['status'=>1,'message'=>'Password Updated'],200);
        }
        else{
            return response()->json(['status'=>0,'message'=>'Incorrect Password'],400);
        }
    }

    public function restaurantslocation(Request $request)
    {
        $trucklocation=User::select('lat','lang')->where('type','1')->first();
        if(!empty($trucklocation))
        {
            return response()->json(['status'=>1,'message'=>'Location','data'=>$trucklocation],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'No data found'],200);
        }
    }

    public function isopen(Request $request)
    {
        $isopen=User::select('is_open')->where('type','1')->first();

        if(!empty($isopen))
        {
            if ($isopen->is_open == "1") {
                return response()->json(['status'=>1,'message'=>'restaurants is open'],200);
            } else {
                return response()->json(['status'=>0,'message'=>'Store is currently closed. Try after some time'],200);
            }
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'Something Went wrong'],200);
        }
    }

    public function forgotPassword(Request $request)
    {
        if($request->email == ""){
            return response()->json(["status"=>0,"message"=>"Email id is required"],400);
        }

        $checklogin=User::where('email',$request['email'])->first();
        
        if(empty($checklogin))
        {
            return response()->json(['status'=>0,'message'=>'Email does not exist'],400);
        } elseif ($checklogin->google_id != "" OR $checklogin->facebook_id != "") {
            return response()->json(['status'=>0,'message'=>"Your account is registered as social login. Try with that"],200);
        } else {
            try{
                $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 8 );
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
                return response()->json(['status'=>0,'message'=>'Something went wrong while sending email. Please try again'],200);
            }
        }

    }
}
