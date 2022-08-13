@extends('theme.default')

@section('content')

<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Payments</a></li>
        </ol>
    </div>
</div>
<!-- row -->

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if (\Session::has('success'))
                <div class="alert alert-success w-100 alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {!! \Session::get('success') !!}
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">All Payments</h4>
                    <div class="table-responsive" id="table-display">
                        <table class="table table-striped table-bordered zero-configuration">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($getpayment as $payment) {
                                ?>
                                <tr>
                                    <td>{{$payment->id}}</td>
                                    <td>{{$payment->payment_name}}</td>
                                    <td>
                                        @if($payment->is_available == '1')
                                            <a class="badge badge-info px-2" onclick="StatusUpdate('{{$payment->id}}','2')" style="color: #fff;">Active</a>
                                        @else
                                            <a class="badge badge-primary px-2" onclick="StatusUpdate('{{$payment->id}}','1')" style="color: #fff;">Deactive</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->payment_name != 'COD')
                                            <a data-toggle="tooltip" href="{{URL::to('admin/manage-payment/'.$payment->id)}}" data-original-title="View">
                                                <span class="badge badge-warning">View</span>
                                            </a>
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                                <?php
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
@endsection
@section('script')

<script type="text/javascript">
function StatusUpdate(id,status) {
    swal({
        title: "Are you sure?",
        text: "Are you sure want to change ?",
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
                url:"{{ URL::to('admin/payment/status') }}",
                data: {
                    id: id,
                    status: status
                },
                method: 'POST',
                success: function(response) {
                    if (response == 1) {
                        swal({
                            title: "Approved!",
                            text: "Payment status has been changed.",
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

</script>
@endsection