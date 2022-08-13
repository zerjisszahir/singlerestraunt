<!DOCTYPE html>

<html>



<head>

	<title>{{$getabout->title}}</title>



	<!-- meta tag -->

	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">



	<meta property="og:title" content="Single restaurant food ordering Website and Delivery Boy App with Admin Panel" />

	<meta property="og:description" content="Restaurant food ordering Website is a catalyst for the food industry. The website lets you (a restaurateur) connect with the customers who wish to either get food delivered or pick-up food. The website lets you track customers’ order till the food delivery. With this website you can easily manage the entire restaurant food business to achieve maximum growth." />

	<meta property="og:image" content='{!! asset("public/front/images/banner.png") !!}' />



	<!-- favicon-icon  -->

	<link rel="icon" href='{!! asset("public/images/about/".$getabout->favicon) !!}' type="image/x-icon">



	<!-- font-awsome css  -->

	<link rel="stylesheet" type="text/css" href="{!! asset('public/front/css/font-awsome.css') !!}">



	<!-- fonts css -->

	<link rel="stylesheet" type="text/css" href="{!! asset('public/front/fonts/fonts.css') !!}">



	<!-- bootstrap css -->

	<link rel="stylesheet" type="text/css" href="{!! asset('public/front/css/bootstrap.min.css') !!}">



	<!-- fancybox css -->

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />



	<!-- owl.carousel css -->

	<link rel="stylesheet" type="text/css" href="{!! asset('public/front/css/owl.carousel.min.css') !!}">



	<link href="{!! asset('public/assets/plugins/sweetalert/css/sweetalert.css') !!}" rel="stylesheet">

	<!-- style css  -->

	<link rel="stylesheet" type="text/css" href="{!! asset('public/front/css/style.css') !!}">



	<!-- responsive css  -->

	<link rel="stylesheet" type="text/css" href="{!! asset('public/front/css/responsive.css') !!}">



</head>



<body>



	<!--*******************

	    Preloader start

	********************-->

	<div id="preloader" style="display: none;">

	    <div class="loader">

	        <img src="{!! asset('public/front/images/loader.gif') !!}">

	    </div>

	</div>

	<!--*******************

	    Preloader end

	********************-->



	<!-- navbar -->

	<header>

		<nav class="navbar navbar-expand-lg">

			<div class="container">

				<a class="navbar-brand" href="{{URL::to('/')}}"><img src='{!! asset("public/images/about/".$getabout->logo) !!}' alt=""></a>

				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">

					<div class="menu-icon">

						<div class="bar1"></div>

						<div class="bar2"></div>

						<div class="bar3"></div>

					</div>

				</button>

				<div class="collapse navbar-collapse justify-content-end" id="navbarNav">

					<ul class="navbar-nav">

						<li class="nav-item {{ request()->is('/') ? 'active' : '' }}">

							<a class="nav-link" href="{{URL::to('/')}}">Home</a>

						</li>

						<li class="nav-item {{ request()->is('product') ? 'active' : '' }}">

							<a class="nav-link" href="{{URL::to('/product')}}">Our Products</a>

						</li>

						@if (Session::get('id'))

						

							<li class="nav-item {{ request()->is('orders') ? 'active' : '' }}">

								<a class="nav-link" href="{{URL::to('/orders')}}">My Orders</a>

							</li>

							<li class="nav-item {{ request()->is('favorite') ? 'active' : '' }}">

								<a class="nav-link" href="{{URL::to('/favorite')}}">Favourite List</a>

							</li>

							<li class="nav-item {{ request()->is('wallet') ? 'active' : '' }}">

								<a class="nav-link" href="{{URL::to('/wallet')}}">My Wallet</a>

							</li>

							<li class="nav-item search">

								<form method="get" action="{{URL::to('/search')}}">

									<div class="search-input">

										<input type="search" name="item" placeholder="Search here" required="">

									</div>

									<button type="submit" class="nav-link"><i class="far fa-search"></i></button>

								</form>

							</li>

							<li class="nav-item cart-btn">

								<a class="nav-link" href="{{URL::to('/cart')}}"><i class="fas fa-shopping-cart"></i><span id="cartcnt">{{Session::get('cart')}}</span></a>

							</li>

						@else 

							<li class="nav-item search">

								<form method="get" action="{{URL::to('/search')}}">

									<div class="search-input">

										<input type="search" name="item" placeholder="Search here" required="">

									</div>

									<button type="submit" class="nav-link"><i class="far fa-search"></i></button>

								</form>

							</li>

							<li class="nav-item cart-btn">

								<a class="nav-link" href="{{URL::to('/signin')}}"><i class="fas fa-shopping-cart"></i></a>

							</li>

						@endif

						@if (Session::get('id'))

							<li class="nav-item dropdown">

								<a class="nav-link dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:void(0)">

									<img src='{!! asset("public/images/profile/".Session::get("profile_image")) !!}' alt="">

								</a>

								<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

									<a class="dropdown-item" href="javascript:void(0)">Hello, {{Session::get('name')}}</a>

									<a class="dropdown-item" href="" data-toggle="modal" data-target="#AddReview">Add Review</a>

									<a class="dropdown-item" href="" data-toggle="modal" data-target="#Refer">Refer and Earn</a>

									<a class="dropdown-item" href="" data-toggle="modal" data-target="#ChangePasswordModal">Change Password</a>

									<a class="dropdown-item" href="{{URL::to('/logout')}}">Logout</a>

								</div>

							</li>

						@else 

							<li class="nav-item">

								<a class="nav-link btn sign-btn" href="{{URL::to('/signin')}}">Login</a>

							</li>

						@endif

						

					</ul>

				</div>

			</div>

		</nav>

	</header>

	<!-- navbar -->

	<div id="success-msg" class="alert alert-dismissible mt-3" style="display: none;">

	    <span id="msg"></span>

	</div>



	<div id="error-msg" class="alert alert-dismissible mt-3" style="display: none;">

	    <span id="ermsg"></span>

	</div>



	@include('cookieConsent::index')