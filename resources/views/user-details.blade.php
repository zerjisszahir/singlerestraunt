@extends('theme.default')

@section('content')
<!-- row -->

<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Product Photos</a></li>
        </ol>
    </div>
</div>
<!-- row -->

<div class="container-fluid">
    <!-- End Row -->

    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <img src='{!! asset("public/images/profile/".$getusers->profile_image) !!}' width="100px" class="rounded-circle" alt="">
                        <h5 class="mt-3 mb-1">{{$getusers->name}}</h5>
                        <p class="m-0">{{$getusers->email}}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <img src='{!! asset("public/front/images/wallet.png") !!}' width="100px" alt="">
                        <h5 class="mt-3 mb-1">Wallet Balance</h5>
                        <p class="m-0">{{Auth::user()->currency}}{{number_format($getusers->wallet, 2)}}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <img src='{!! asset("public/front/images/shopping-cart.png") !!}' width="100px" alt="">
                        <h5 class="mt-3 mb-1">{{count($getorders)}}</h5>
                        <p class="m-0">Order</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <img src='{!! asset("public/front/images/referral-admin.png") !!}' width="100px" alt="">
                        <h5 class="mt-3 mb-1">{{$getusers->referral_code}}</h5>
                        <p class="m-0">Referral Code</p>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">All Ordres</h4>
                    <div class="table-responsive" id="table-display">
                        <table class="table table-striped table-bordered zero-configuration">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order Number</th>
                                    <th>Payment Type</th>
                                    <th>Payment ID</th>
                                    <th>Order Type</th>
                                    <th>Order Status</th>
                                    <th>Order Assigned To</th>
                                    <th>Created at</th>
                                    <th>Change Order Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($getorders as $orders) {
                                ?>
                                <tr id="dataid{{$orders->id}}">
                                    <td>{{$i}}</td>
                                    <td>{{$orders->order_number}}</td>
                                    <td>
                                        @if($orders->payment_type =='0')
                                              COD
                                        @elseif($orders->payment_type =='3')
                                            Wallet
                                        @else
                                              Online
                                        @endif
                                    </td>
                                    <td>
                                        @if($orders->razorpay_payment_id == '')
                                            --
                                        @else
                                            {{$orders->razorpay_payment_id}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($orders->order_type == 1)
                                            Delivery
                                        @else
                                            Pickup
                                        @endif
                                    </td>
                                    <td>
                                        @if($orders->status == '1')
                                            Order Received
                                        @elseif ($orders->status == '2')
                                            On the way
                                        @elseif ($orders->status == '3')
                                            Assigned to Driver
                                        @elseif ($orders->status == '4')
                                            Delivered
                                        @else
                                            Cancelled
                                        @endif
                                    </td>
                                    <td>
                                        @if ($orders->name == "")
                                            --
                                        @else
                                            {{$orders->name}}
                                        @endif
                                    </td>
                                    <td>{{$orders->created_at}}</td>
                                    <td>
                                        @if($orders->status == '1')
                                            <a ddata-toggle="tooltip" data-placement="top" onclick="StatusUpdate('{{$orders->id}}','2')" title="" data-original-title="Order Received">
                                                <span class="badge badge-secondary px-2" style="color: #fff;">Order Received</span>
                                            </a>
                                        @elseif ($orders->status == '2')
                                            @if ($orders->order_type == '2')
                                                <a class="badge badge-primary px-2" onclick="StatusUpdate('{{$orders->id}}','4')" style="color: #fff;">Pickup</a>
                                            @else
                                                <a class="open-AddBookDialog badge badge-primary px-2" data-toggle="modal" data-id="{{$orders->id}}" data-target="#myModal" style="color: #fff;">Assign To Driver</a>
                                            @endif
                                        @elseif ($orders->status == '3')
                                            <a ddata-toggle="tooltip" data-placement="top" title="" data-original-title="Out for Delivery">
                                                <span class="badge badge-success px-2" style="color: #fff;">Assigned to Driver</span>
                                            </a>
                                        @elseif ($orders->status == '4')
                                            <a ddata-toggle="tooltip" data-placement="top" title="" data-original-title="Out for Delivery">
                                                <span class="badge badge-success px-2" style="color: #fff;">Delivered</span>
                                            </a>
                                        @else
                                            <span class="badge badge-danger px-2">Cancelled</span>
                                        @endif

                                        @if ($orders->status != '4' && $orders->status != '5' && $orders->status != '6')
                                            <a data-toggle="tooltip" data-placement="top" onclick="StatusUpdate('{{$orders->id}}','6')" title="" data-original-title="Cancel">
                                                <span class="badge badge-danger px-2" style="color: #fff;">Cancel</span>
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <span>
                                            <a data-toggle="tooltip" href="{{URL::to('admin/invoice/'.$orders->id)}}" data-original-title="View">
                                                <span class="badge badge-warning">View</span>
                                            </a>
                                        </span>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- #/ container -->

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