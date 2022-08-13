<!-- Modal Change Password-->

<div class="modal fade text-left" id="ChangePasswordModal" tabindex="-1" role="dialog" aria-labelledby="RditProduct"

aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <label class="modal-title text-text-bold-600" id="RditProduct">Change Password</label>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div id="errors" style="color: red;"></div>

      

      <form method="post" id="change_password_form">

      {{csrf_field()}}

        <div class="modal-body">

          <label>Old passwod </label>

          <div class="form-group">

              <input type="password" placeholder="Old password" class="form-control" name="oldpassword" id="oldpassword">

          </div>



          <label>New password </label>

          <div class="form-group">

              <input type="password" placeholder="New password" class="form-control" name="newpassword" id="newpassword">

          </div>



          <label>Confirm password </label>

          <div class="form-group">

              <input type="password" placeholder="Confirm password" class="form-control" name="confirmpassword" id="confirmpassword">

          </div>



        </div>

        <div class="modal-footer">

          <input type="reset" class="btn open comman" data-dismiss="modal"

          value="Close">

          <input type="button" class="btn open comman" onclick="changePassword()"  value="Submit">

        </div>

      </form>

    </div>

  </div>

</div>



<!-- Modal Add Review-->

<div class="modal fade text-left" id="AddReview" tabindex="-1" role="dialog" aria-labelledby="RditProduct"

aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <label class="modal-title text-text-bold-600" id="RditProduct">Add Review</label>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div id="errorr" style="color: red;"></div>

      

      <form method="post" id="change_password_form">

      {{csrf_field()}}

        <div class="modal-body">

          	<div class="rating"> 

	        	<input type="radio" name="rating" value="5" id="star5"><label for="star5">☆</label> 

	        	<input type="radio" name="rating" value="4" id="star4"><label for="star4">☆</label> 

	        	<input type="radio" name="rating" value="3" id="star3"><label for="star3">☆</label> 

	        	<input type="radio" name="rating" value="2" id="star2"><label for="star2">☆</label> 

	        	<input type="radio" name="rating" value="1" id="star1"><label for="star1">☆</label>

	        </div>



          <label>Comment </label>

          <div class="form-group">

          	<textarea class="form-control" name="comment" id="comment" rows="5" required=""></textarea>

          	<input type="hidden" name="user_id" id="user_id" class="form-control" value="{{Session::get('id')}}">

          </div>



        </div>

        <div class="modal-footer">

          <input type="reset" class="btn open comman" data-dismiss="modal"

          value="Close">

          <input type="button" class="btn open comman" onclick="addReview()"  value="Submit">

        </div>

      </form>

    </div>

  </div>

</div>





<!-- Modal Add Refer-->

<div class="modal fade text-left" id="Refer" tabindex="-1" role="dialog" aria-labelledby="RditProduct"

aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <label class="modal-title text-text-bold-600" id="RditProduct">Refer and Earn</label>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div id="errorr" style="color: red;"></div>

      

        <div class="modal-body">

          	<img src='{!! asset("public/front/images/referral.png") !!}' alt="img1" border="0">

          	<p style="color: #464648;font-size: 16px;font-weight: 500;margin-bottom: 0; text-align: center;">Share this code with a friend and you both could be eligible for <span style="color: #fd3b2f">{{$getdata->currency}}{{number_format(Session::get('referral_amount'), 2)}}</span> bonus amount under our Referral Program.</p>

          	<hr>

          	<div class="text-center mt-2">

	          	<label>Your Referral Code </label>

	          	<p style="color: #fd3b2f;font-size: 35px;font-weight: 500;margin-bottom: 0; text-align: center;">{{Session::get('referral_code')}}</p>

          	</div>



          	<p style="text-align: center;">-----OR-----</p>



          	<div class="text-center mt-2">

          		<label>Use this link to share </label>

          		<div class="form-group">

          			<input type="text" class="form-control text-center" value="{{url('/signup')}}/?referral_code={{Session::get('referral_code')}}" id="myInput" readonly="">



          			<div class="tooltip-refer">

          				<button onclick="myFunction()" class="btn btn-outline-secondary" onmouseout="outFunc()">

	          			  <span class="tooltiptext" id="myTooltip">Copy to clipboard</span>

	          			  Copy link

          			  </button>

          			</div>

          		</div>

          	</div>



        </div>

    </div>

  </div>

</div>





<footer>

	<div class="container d-flex justify-content-between flex-wrap">

		<div class="footer-head">

			<div class="footer-logo"><img src='{!! asset("public/images/about/".$getabout->footer_logo) !!}' alt=""></div>

			<p>{!! \Illuminate\Support\Str::limit(htmlspecialchars($getabout->about_content, ENT_QUOTES, 'UTF-8'), $limit = 200, $end = '...') !!}</p>

		</div>

		<div class="footer-socialmedia">

			@if($getabout->fb != "")

				<a href="{{$getabout->fb}}" target="_blank"><i class="fab fa-facebook-f"></i></a>

			@endif



			@if($getabout->twitter != "")

				<a href="{{$getabout->twitter}}" target="_blank"><i class="fab fa-twitter"></i></a>

			@endif



			@if($getabout->insta != "")

				<a href="{{$getabout->insta}}" target="_blank"><i class="fab fa-instagram"></i></a>

			@endif

		</div>

		<div class="download-app">

			<p>Download the App</p>

			<div class="download-app-wrap">

				@if($getabout->ios != "")

					<div class="download-app-icon">

						<a href="{{$getabout->ios}}" target="_blank"><img src="{!! asset('public/front/images/apple-store.svg') !!}" alt=""></a>

					</div>

				@endif



				@if($getabout->android != "")

					<div class="download-app-icon">

						<a href="{{$getabout->android}}" target="_blank"><img src="{!! asset('public/front/images/play-store.png') !!}" alt=""></a>

					</div>

				@endif

			</div>

		</div>

	</div>

	<div class="copy-right text-center">

		<a href="{{URL::to('/privacy')}}" style="color: #fff;"> Privacy Policy </a>

		<p>{{$getabout->copyright}} <br> Designed & Developed by <a href="https://infotechgravity.com" target="_blank" style="color: #000;"><b>Gravity Infotech</b>.</a></p>

	</div>

</footer>



<a onclick="topFunction()" id="myBtn" title="Go to top" style="display: block;"><i class="fad fa-long-arrow-alt-up"></i></a>



<!-- footer -->





<!-- View order btn -->

@if (Session::get('cart') && !request()->is('cart'))

	<a href="{{URL::to('/cart')}}" class="view-order-btn">View My Order</a>

@else

	<a href="{{URL::to('/cart')}}" class="view-order-btn" style="display: none;">View My Order</a>

@endif

<!-- View order btn -->





<!-- jquery -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>



<!-- bootstrap js -->

<script src="{!! asset('public/front/js/bootstrap.bundle.js') !!}"></script>



<!-- owl.carousel js -->

<script src="{!! asset('public/front/js/owl.carousel.min.js') !!}"></script>



<!-- lazyload js -->

<script src="{!! asset('public/front/js/lazyload.js') !!}"></script>



<!-- custom js -->

<script src="{!! asset('public/front/js/custom.js') !!}"></script>



<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>



<script src="{!! asset('public/assets/plugins/sweetalert/js/sweetalert.min.js') !!}"></script>

<script src="{!! asset('public/assets/plugins/sweetalert/js/sweetalert.init.js') !!}"></script>



<script type="text/javascript">



	function myFunction() {

	  var copyText = document.getElementById("myInput");

	  copyText.select();

	  copyText.setSelectionRange(0, 99999);

	  document.execCommand("copy");

	  

	  var tooltip = document.getElementById("myTooltip");

	  tooltip.innerHTML = "Copied";

	}



	function outFunc() {

	  var tooltip = document.getElementById("myTooltip");

	  tooltip.innerHTML = "Copy to clipboard";

	}



	function changePassword(){

	    var oldpassword=$("#oldpassword").val();

	    var newpassword=$("#newpassword").val();

	    var confirmpassword=$("#confirmpassword").val();

	    var CSRF_TOKEN = $('input[name="_token"]').val();

	    

	    $('#preloader').show();

	    $.ajax({

	        headers: {

	            'X-CSRF-Token': CSRF_TOKEN 

	        },

	        url:"{{ url('/home/changePassword') }}",

	        method:'POST',

	        data:{'oldpassword':oldpassword,'newpassword':newpassword,'confirmpassword':confirmpassword},

	        dataType:"json",

	        success:function(data){

	        	$("#preloader").hide();

	            if(data.error.length > 0)

	            {

	                var error_html = '';

	                for(var count = 0; count < data.error.length; count++)

	                {

	                    error_html += '<div class="alert alert-danger mt-1">'+data.error[count]+'</div>';

	                }

	                $('#errors').html(error_html);

	                setTimeout(function(){

	                    $('#errors').html('');

	                }, 10000);

	            }

	            else

	            {

	                location.reload();

	            }

	        },error:function(data){

	           

	        }

	    });

	}

	var ratting = "";

	$('.rating input').on('click', function(){

        ratting = $(this).val();

	});

	function addReview(){



        var comment=$("#comment").val();

        var user_id=$("#user_id").val();



        var CSRF_TOKEN = $('input[name="_token"]').val();



		// $('#preloader').show();

		$.ajax({

            headers: {

                'X-CSRF-Token': CSRF_TOKEN 

            },

            url:"{{ url('/home/addreview') }}",

            method:'POST',

            data: 'comment='+comment+'&ratting='+ratting+'&user_id='+user_id,

            dataType: 'json',

            success:function(data){

	        	$("#preloader").hide();

	            if(data.error.length > 0)

	            {

	                var error_html = '';

	                for(var count = 0; count < data.error.length; count++)

	                {

	                    error_html += '<div class="alert alert-danger mt-1">'+data.error[count]+'</div>';

	                }

	                $('#errorr').html(error_html);

	                setTimeout(function(){

	                    $('#errorr').html('');

	                }, 10000);

	            }

	            else

	            {

	                location.reload();

	            }

	        },error:function(data){

	           

	        }

        });

	}



	function contact() {

        var firstname=$("#firstname").val();

        var lastname=$("#lastname").val();

        var email=$("#email").val();

        var message=$("#message").val();

        var CSRF_TOKEN = $('input[name="_token"]').val();

        $('#preloader').show();

        $.ajax({

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            },

            url:"{{ URL::to('/home/contact') }}",

            data: {

                firstname: firstname,

                lastname: lastname,

                email: email,

                message: message

            },

            method: 'POST', //Post method,

            dataType: 'json',

            success: function(response) {

            	$("#preloader").hide();

                if (response.status == 1) {

                    $('#msg').text(response.message);

                    $('#success-msg').addClass('alert-success');

                    $('#success-msg').css("display","block");

                    $("#contactform")[0].reset();

                    setTimeout(function() {

                        $("#success-msg").hide();

                    }, 5000);

                } else {

                    $('#ermsg').text(response.message);

                    $('#error-msg').addClass('alert-danger');

                    $('#error-msg').css("display","block");



                    setTimeout(function() {

                        $("#error-msg").hide();

                    }, 5000);

                }

            }

        })

    };

	function AddtoCart(id,user_id) {



		var price = $('#price').val();

		var item_notes = $('#item_notes').val();



        var addons_id = ($('.Checkbox:checked').map(function() {

            return this.value;

        }).get().join(', '));

        $('#preloader').show();

	    $.ajax({

	        headers: {

	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

	        },

	        url:"{{ URL::to('/product/addtocart') }}",

	        data: {

	            item_id: id,

	            addons_id: addons_id,

	            qty: '1',

	            price: price,

	            item_notes: item_notes,

	            user_id: user_id

	        },

	        method: 'POST', //Post method,

	        dataType: 'json',

	        success: function(response) {

	        	$("#preloader").hide();

	            if (response.status == 1) {

	            	$('#cartcnt').text(response.cartcnt);

	                $('#msg').text(response.message);

	                $('#success-msg').addClass('alert-success');

	                $('#success-msg').css("display","block");

	                $('.view-order-btn').show();



	                setTimeout(function() {

	                    $("#success-msg").hide();

	                }, 5000);

	            } else {

	                $('#ermsg').text(response.message);

	                $('#error-msg').addClass('alert-danger');

	                $('#error-msg').css("display","block");



	                setTimeout(function() {

	                    $("#success-msg").hide();

	                }, 5000);

	            }

	        },

	        error: function(error) {



	            // $('#errormsg').show();

	        }

	    })

	};

	function Unfavorite(id,user_id) {

	    swal({

	        title: "Are you sure?",

	        text: "Do you want to Unfavorite this item ?",

	        type: "warning",

	        showCancelButton: true,

	        confirmButtonClass: "btn-danger",

	        confirmButtonText: "Yes, Unfavorite it!",

	        cancelButtonText: "No, cancel plz!",

	        closeOnConfirm: false,

	        closeOnCancel: false,

	        showLoaderOnConfirm: true,

	    },

	    function(isConfirm) {

	        if (isConfirm) {

	            $.ajax({

	                headers: {

	                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

	                },

	                url:"{{ URL::to('/product/unfavorite') }}",

	                data: {

                        item_id: id,

                        user_id: user_id

                    },

	                method: 'POST',

	                success: function(response) {

	                    if (response == 1) {

	                        swal({

	                            title: "Approved!",

	                            text: "Item has been unfavorite.",

	                            type: "success",

	                            showCancelButton: true,

	                            confirmButtonClass: "btn-danger",

	                            confirmButtonText: "Ok",

	                            closeOnConfirm: false,

	                            showLoaderOnConfirm: true,

	                        },

	                        function(isConfirm) {

	                            if (isConfirm) {

	                                swal.close();

	                                location.reload();

	                            }

	                        });

	                    } else {

	                        swal("Cancelled", "Something Went Wrong :(", "error");

	                    }

	                },

	                error: function(e) {

	                    swal("Cancelled", "Something Went Wrong :(", "error");

	                }

	            });

	        } else {

	            swal("Cancelled", "Your record is safe :)", "error");

	        }

	    });

	}



	function MakeFavorite(id,user_id) {

	    swal({

	        title: "Are you sure?",

	        text: "Do you want to favorite this item ?",

	        type: "warning",

	        showCancelButton: true,

	        confirmButtonClass: "btn-danger",

	        confirmButtonText: "Yes, Make it favorite!",

	        cancelButtonText: "No, cancel plz!",

	        closeOnConfirm: false,

	        closeOnCancel: false,

	        showLoaderOnConfirm: true,

	    },

	    function(isConfirm) {

	        if (isConfirm) {

	            $.ajax({

	                headers: {

	                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

	                },

	                url:"{{ URL::to('/product/favorite') }}",

	                data: {

                        item_id: id,

                        user_id: user_id

                    },

	                method: 'POST',

	                success: function(response) {

	                    if (response == 1) {

	                        swal({

	                            title: "Approved!",

	                            text: "Item has been added in favorite list.",

	                            type: "success",

	                            showCancelButton: true,

	                            confirmButtonClass: "btn-danger",

	                            confirmButtonText: "Ok",

	                            closeOnConfirm: false,

	                            showLoaderOnConfirm: true,

	                        },

	                        function(isConfirm) {

	                            if (isConfirm) {

	                                swal.close();

	                                location.reload();

	                            }

	                        });

	                    } else {

	                        swal("Cancelled", "Something Went Wrong :(", "error");

	                    }

	                },

	                error: function(e) {

	                    swal("Cancelled", "Something Went Wrong :(", "error");

	                }

	            });

	        } else {

	            swal("Cancelled", "Your record is safe :)", "error");

	        }

	    });

	};



	function OrderCancel(id) {

	    swal({

	        title: "Are you sure?",

	        text: "Order amount will be transferred to your wallet",

	        type: "warning",

	        showCancelButton: true,

	        confirmButtonClass: "btn-danger",

	        confirmButtonText: "Yes, Cancel it!",

	        cancelButtonText: "No, leave plz!",

	        closeOnConfirm: false,

	        closeOnCancel: false,

	        showLoaderOnConfirm: true,

	    },

	    function(isConfirm) {

	        if (isConfirm) {

	            $.ajax({

	                headers: {

	                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

	                },

	                url:"{{ URL::to('/order/ordercancel') }}",

	                data: {

                        order_id: id,

                    },

	                method: 'POST',

	                success: function(response) {

	                    if (response == 1) {

	                        swal({

	                            title: "Approved!",

	                            text: "Order has been cancelled.",

	                            type: "success",

	                            showCancelButton: true,

	                            confirmButtonClass: "btn-danger",

	                            confirmButtonText: "Ok",

	                            closeOnConfirm: false,

	                            showLoaderOnConfirm: true,

	                        },

	                        function(isConfirm) {

	                            if (isConfirm) {

	                                swal.close();

	                                location.reload();

	                            }

	                        });

	                    } else {

	                        swal("Cancelled", "Something Went Wrong :(", "error");

	                    }

	                },

	                error: function(e) {

	                    swal("Cancelled", "Something Went Wrong :(", "error");

	                }

	            });

	        } else {

	            swal("Cancelled", "Your record is safe :)", "error");

	        }

	    });

	};



	function codeAddress() {

        $.ajax({

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            },

            type: 'GET',

            url:"{{ URL::to('/cart/isopenclose') }}",

            success: function(response) {

                if (response.status == 0) {

                    $('.open').hide();

                    $('.openmsg').show();

                } else {

                    $('.openmsg').hide();

                }

            }

        });

    }

    window.onload = codeAddress;

</script>

@yield('script')

</body>



</html>