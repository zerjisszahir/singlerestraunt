@extends('theme.default')

@section('content')


    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-1">
                    <a href="{{URL::to('/admin/category')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">Categories</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($getcategory)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-list-alt"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-2">
                    <a href="{{URL::to('/admin/item')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">Items</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($getitems)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-cutlery"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-3">
                    <a href="{{URL::to('/admin/addons')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">Add-ons</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($addons)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-plus"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-4">
                    <a href="{{URL::to('/admin/users')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">Users</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($getusers)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-users"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-4">
                    <a href="{{URL::to('/admin/orders')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">Orders</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($getorders)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-shopping-cart"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-3">
                    <a href="{{URL::to('/admin/reviews')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">Reviews</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($getreview)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-star"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-2">
                    <a href="{{URL::to('/admin/promocode')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">Promocodes</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($getpromocode)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-gift"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-1">
                    <a href="{{URL::to('/admin/driver')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">Drivers</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($driver)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-car"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-1">
                    <a href="{{URL::to('/admin/pincode')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">Pincodes</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($getpincode)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-map-pin"></i></span>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-2">
                    <a href="{{URL::to('/admin/orders')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">Tax</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{Auth::user()->currency}}{{ number_format($order_tax, 2) }}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-calculator"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-3">
                    <a href="{{URL::to('/admin/orders')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">Earnings</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{Auth::user()->currency}}{{ number_format($order_total-$order_tax, 2) }}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-usd"></i></span>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-4">
                    <a href="{{URL::to('/admin/banner')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">Promotion Banners</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($banners)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-bullhorn"></i></span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Today's Orders</h4>
                        <div class="table-responsive" id="table-display">
                            @include('theme.todayorderstable')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="post" id="assign">
                {{csrf_field()}}
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category_id" class="col-form-label">Order ID:</label>
                        <input type="text" class="form-control" id="bookId" name="bookId" readonly="">
                    </div>
                    <div class="form-group">
                        <label for="category_id" class="col-form-label">Select Driver:</label>
                        <select class="form-control" name="driver_id" id="driver_id" required="">
                            <option value="">Select Driver</option>
                            @foreach ($getdriver as $driver)
                                <option value="{{$driver->id}}">{{$driver->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="assign()" data-dismiss="modal">Save</button>
                </div>
                </form>
            </div>

        </div>
    </div>
    <!-- #/ container -->
@endsection
@section('script')
<script type="text/javascript">
    $('.table').dataTable({
      aaSorting: [[0, 'DESC']]
    });
    function DeleteData(id) {
        swal({
            title: "Are you sure?",
            text: "Do you want to delete this Order ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, delete it!",
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
                    url:"{{ URL::to('admin/orders/destroy') }}",
                    data: {
                        id: id
                    },
                    method: 'POST',
                    success: function(response) {
                        if (response == 1) {
                            swal({
                                title: "Approved!",
                                text: "Order has been deleted.",
                                type: "success",
                                showCancelButton: true,
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "Ok",
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true,
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    $('#dataid'+id).remove();
                                    swal.close();
                                    // location.reload();
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

    function StatusUpdate(id,status) {
        swal({
            title: "Are you sure?",
            text: "Do you want to change status?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, change it!",
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
                    url:"{{ URL::to('admin/orders/update') }}",
                    data: {
                        id: id,
                        status: status
                    },
                    method: 'POST', //Post method,
                    dataType: 'json',
                    success: function(response) {
                        swal({
                            title: "Approved!",
                            text: "Status has been changed.",
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
                    },
                    error: function(e) {
                        swal("Cancelled", "Something Went Wrong :(", "error");
                    }
                });
            } else {
                swal("Cancelled", "Something went wrong :)", "error");
            }
        });
    }

    $(document).on("click", ".open-AddBookDialog", function () {
         var myBookId = $(this).data('id');
         $(".modal-body #bookId").val( myBookId );
    });

    function assign(){     
        var bookId=$("#bookId").val();
        var driver_id = $('#driver_id').val();
        var CSRF_TOKEN = $('input[name="_token"]').val();
        $('#preloader').show();
        $.ajax({
            headers: {
                'X-CSRF-Token': CSRF_TOKEN 
            },
            url:"{{ URL::to('admin/orders/assign') }}",
            method:'POST',
            data:{'bookId':bookId,'driver_id':driver_id},
            dataType:"json",
            success:function(data){
                $('#preloader').hide();
                if (data == 1) {
                    location.reload();
                }
            },error:function(data){
               
            }
        });
    }
</script>
@endsection