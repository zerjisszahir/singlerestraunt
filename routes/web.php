<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('front.home');
});

Route::get('auth/facebook', 'Auth\SocialController@redirectToFacebook');
Route::get('auth/facebook/callback', 'Auth\SocialController@handleFacebookCallback');

Route::get('auth/google', 'Auth\GoogleController@redirectToGoogle');
Route::get('auth/google/callback', 'Auth\GoogleController@handleGoogleCallback');

Route::post('auth', 'HomeController@auth');

Route::group(['namespace' => 'front'], function () {
	Route::get('/', 'HomeController@index');
	Route::get('/405', 'HomeController@notallow');
	Route::post('/home/contact', 'HomeController@contact');
	Route::post('/home/checkpincode', 'HomeController@checkpincode');

	Route::get('/product', 'ItemController@index');
	Route::get('/product-details/{id}', 'ItemController@productdetails');
	Route::get('/product/{id}', 'ItemController@show');
	Route::post('/product/favorite', 'ItemController@favorite');
	Route::post('/product/unfavorite', 'ItemController@unfavorite');
	Route::post('/product/addtocart', 'ItemController@addtocart');
	Route::get("/search","ItemController@search");

	Route::get('/cart', 'CartController@index');
	Route::post('/cart/qtyupdate','CartController@qtyupdate');
	Route::post('/cart/applypromocode', 'CartController@applypromocode');
	Route::post('/cart/deletecartitem', 'CartController@deletecartitem');
	Route::post('/cart/removepromocode', 'CartController@removepromocode');
	Route::get('/cart/isopenclose', 'CartController@isopenclose');

	Route::get('/favorite', 'FavoriteController@index');

	Route::get('/orders', 'OrderController@index');
	Route::post('/orders/cashondelivery', 'OrderController@cashondelivery');
	Route::post('/orders/walletorder', 'OrderController@walletorder');
	Route::post('/order/ordercancel', 'OrderController@ordercancel');
	Route::get('/order-details/{id}', 'OrderController@orderdetails');
	
	Route::get('/signin', 'UserController@index');
	Route::post('/signin/login', 'UserController@login');
	Route::get ( '/logout', 'UserController@logout' );
	Route::get('/signup', 'UserController@signup');
	Route::post('/signup/signup', 'UserController@register');

	Route::get('/wallet', 'UserController@wallet');
	
	Route::get('/forgot-password', 'UserController@forgot_password');
	Route::post('/forgot-password/forgot-password', 'UserController@forgotpassword');
	Route::post('/home/changePassword', 'UserController@changePassword');
	Route::post('/home/addreview', 'UserController@addreview');
	Route::get('/email-verify', 'UserController@email_verify');
	Route::get('/resend-email', 'UserController@resend_email');
	Route::post('/email-verification', 'UserController@email_verification');

	// Get Route For Show Payment Form
	Route::get('/paywithrazorpay', 'RazorpayController@payWithRazorpay')->name('paywithrazorpay');
	// Post Route For Makw Payment Request
	Route::post('/payment', 'RazorpayController@payment')->name('payment');
	
	Route::post('stripe-payment/charge', 'CheckoutController@charge');

	Route::get('/privacypolicy', 'PrivacyPolicyController@index');
	Route::get('/privacy', 'PrivacyPolicyController@privacy');

	Route::get('/termscondition', 'TermsController@index');
	Route::get('/terms', 'TermsController@terms');

	Route::get('/aboutus', 'AboutController@index');
	Route::get('/about', 'AboutController@about');
});
Route::get('/auth', function () {
	return view('/auth');
});
Route::group(['prefix' => 'admin', 'namespace' => 'admin'], function () {
	

	Route::get('/', function () {
		return view('auth.login');
	});

	Route::group(['middleware' => ['AdminAuth']],function(){
		Route::get('home', 'AdminController@home');
		Route::post('changePassword', 'AdminController@changePassword');
		Route::post('settings', 'AdminController@settings');
		Route::get('getorder', 'AdminController@getorder');
		Route::get('clearnotification', 'AdminController@clearnotification');

		Route::get('slider', 'SliderController@index');
		Route::post('slider/store', 'SliderController@store');
		Route::get('slider/list', 'SliderController@list');
		Route::post('slider/show', 'SliderController@show');
		Route::post('slider/update', 'SliderController@update');
		Route::post('slider/destroy', 'SliderController@destroy');

		Route::get('category', 'CategoryController@index');
		Route::post('category/store', 'CategoryController@store');
		Route::get('category/list', 'CategoryController@list');
		Route::post('category/show', 'CategoryController@show');
		Route::post('category/update', 'CategoryController@update');
		Route::post('category/status', 'CategoryController@status');
		Route::post('category/delete', 'CategoryController@delete');

		Route::get('item', 'ItemController@index');
		Route::post('item/store', 'ItemController@store');
		Route::get('item/list', 'ItemController@list');
		Route::post('item/show', 'ItemController@show');
		Route::post('item/update', 'ItemController@update');
		Route::get('item-images/{id}', 'ItemController@itemimages');
		Route::post('item/showimage', 'ItemController@showimage');
		Route::post('item/updateimage', 'ItemController@updateimage');
		Route::get('item/itemimages', 'ItemController@itemimages');
		Route::post('item/storeimages', 'ItemController@storeimages');
		Route::post('item/storeingredientsimages', 'ItemController@storeingredientsimages');
		Route::post('item/destroyimage', 'ItemController@destroyimage');
		Route::post('item/destroyingredients', 'ItemController@destroyingredients');
		Route::post('item/updateingredients', 'ItemController@updateingredients');
		Route::post('item/showingredients', 'ItemController@showingredients');
		Route::post('item/status', 'ItemController@status');
		Route::post('item/delete', 'ItemController@delete');

		Route::get('payment', 'PaymentController@index');
		Route::post('payment/status', 'PaymentController@status');
		Route::get('manage-payment/{id}', 'PaymentController@managepayment');
		Route::post('payment/update', 'PaymentController@update');

		Route::get('addons', 'AddonsController@index');
		Route::post('addons/getitem', 'AddonsController@getitem');
		Route::post('addons/store', 'AddonsController@store');
		Route::get('addons/list', 'AddonsController@list');
		Route::post('addons/show', 'AddonsController@show');
		Route::post('addons/update', 'AddonsController@update');
		Route::post('addons/status', 'AddonsController@status');
		Route::post('addons/delete', 'AddonsController@delete');

		Route::get('users', 'UserController@index');
		Route::post('users/store', 'UserController@store');
		Route::get('users/list', 'UserController@list');
		Route::post('users/show', 'UserController@show');
		Route::post('users/update', 'UserController@update');
		Route::post('users/status', 'UserController@status');
		Route::get('user-details/{id}', 'UserController@userdetails');

		Route::get('orders', 'OrderController@index');
		Route::get('orders/list', 'OrderController@list');
		Route::get('invoice/{id}', 'OrderController@invoice');
		Route::post('orders/destroy', 'OrderController@destroy');
		Route::post('orders/update', 'OrderController@update');
		Route::post('orders/assign', 'OrderController@assign');

		Route::get('reviews', 'RattingController@index');
		Route::get('reviews/list', 'RattingController@list');
		Route::post('reviews/destroy', 'RattingController@destroy');

		Route::get('promocode', 'PromocodeController@index');
		Route::post('promocode/store', 'PromocodeController@store');
		Route::get('promocode/list', 'PromocodeController@list');
		Route::post('promocode/show', 'PromocodeController@show');
		Route::post('promocode/update', 'PromocodeController@update');
		Route::post('promocode/status', 'PromocodeController@status');

		Route::get('pincode', 'PincodeController@index');
		Route::post('pincode/store', 'PincodeController@store');
		Route::get('pincode/list', 'PincodeController@list');
		Route::post('pincode/show', 'PincodeController@show');
		Route::post('pincode/update', 'PincodeController@update');
		Route::post('pincode/destroy', 'PincodeController@destroy');

		Route::get('banner', 'BannerController@index');
		Route::post('banner/store', 'BannerController@store');
		Route::get('banner/list', 'BannerController@list');
		Route::post('banner/show', 'BannerController@show');
		Route::post('banner/update', 'BannerController@update');
		Route::post('banner/destroy', 'BannerController@destroy');

		Route::get('settings', 'AboutController@index');
		Route::post('about/update', 'AboutController@update');

		Route::get('contact', 'ContactController@index');

		Route::get('driver', 'DriverController@index');
		Route::post('driver/store', 'DriverController@store');
		Route::get('driver/list', 'DriverController@list');
		Route::post('driver/show', 'DriverController@show');
		Route::post('driver/update', 'DriverController@update');
		Route::post('driver/status', 'DriverController@status');

		Route::get('report', 'ReportController@index');
		Route::get('report/list', 'ReportController@list');
		Route::post('report/show', 'ReportController@show');
		Route::post('report/destroy', 'ReportController@destroy');
		Route::post('report/update', 'ReportController@update');
		Route::post('report/assign', 'ReportController@assign');

		Route::get('time', 'TimeController@index');
		Route::post('time/store', 'TimeController@store');
		Route::get('time/list', 'TimeController@list');
		Route::post('time/show', 'TimeController@show');
		Route::post('time/update', 'TimeController@update');
		Route::post('time/destroy', 'TimeController@destroy');

		Route::get('privacypolicy', 'PrivacyPolicyController@index');
		Route::post('privacypolicy/update', 'PrivacyPolicyController@update');

		Route::get('termscondition', 'TermsController@index');
		Route::post('termscondition/update', 'TermsController@update');
		
	});

	Route::get('logout', 'AdminController@logout');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

