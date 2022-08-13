@extends('theme.default')

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Report</a></li>
        </ol>
    </div>
</div>
<!-- row -->

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <span id="message"></span>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Report</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="example">
                                <h5 class="box-title m-t-30">Select Date Range</h5>
                                <form method="post" id="get_report">
                                {{csrf_field()}}
                                    <div class="input-daterange input-group" id="date-range">
                                        <input type="text" class="form-control" name="startdate" id="startdate" readonly="" placeholder="Start Date">

                                        <input type="text" class="form-control" name="enddate" id="enddate" readonly="" placeholder="End Date">

                                        <button type="button" class="btn mb-1 btn-flat btn-primary" onclick="GetReport()">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive" id="table-display">
                        
                        @include('theme.reporttable')
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
                <input type="hidden" name="bookId" id="bookId" value=""/>
                <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Mobile</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($getdriver as $driver)
                        <tr>
                            <th scope="row"><input type="checkbox" name="driver_id" id="driver_id" value="{{$driver->id}}"></th>
                            <td>{{$driver->name}}</td>
                            <td>{{$driver->email}}</td>
                            <td>{{$driver->mobile}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>

<script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.bootstrap4.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.colVis.min.js"></script>


<script type="text/javascript">

    var table = $('#example').DataTable( {
        lengthChange: false,
        buttons: [ 'excel']
    } );
 
    table.buttons().container()
        .appendTo( '#example_wrapper .col-md-6:eq(0)' );

    function GetReport() {
        var startdate=$("#startdate").val();
        var enddate=$("#enddate").val();
        var CSRF_TOKEN = $('input[name="_token"]').val();
        
        if($("#get_report").valid()) {
            $.ajax({
                headers: {
                    'X-CSRF-Token': CSRF_TOKEN 
                },
                url:"{{ url('admin/report/show') }}",
                method:'POST',
                data:{'startdate':startdate,'enddate':enddate},
                beforeSend: function() {
                  $("#loading-image").show();
                },
                success:function(data){
                    $('#table-display').html(data);
                    var table = $('#example').DataTable( {
                        lengthChange: false,
                        buttons: [ 'excel']
                    } );
                 
                    table.buttons().container()
                        .appendTo( '#example_wrapper .col-md-6:eq(0)' );
                },error:function(data){
                   
                }
            });
        }
    }

    function ReportTable() {
        $.ajax({
            url:"{{ URL::to('admin/report/list') }}",
            method:'get',
            success:function(data){
                $('#table-display').html(data);
                // $(".zero-configuration").DataTable();
                var table = $('#example').DataTable( {
                    lengthChange: false,
                    buttons: [ 'excel']
                } );
             
                table.buttons().container()
                    .appendTo( '#example_wrapper .col-md-6:eq(0)' );
            }
        });
    }
    function DeleteData(id) {
        // dd(id);
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

        var driver_id = [];
        $.each($("input[name='driver_id']:checked"), function(){
            driver_id.push($(this).val());
        });
        var did = driver_id.join(", ");
        
        var CSRF_TOKEN = $('input[name="_token"]').val();
        // alert(driver_id);
        $.ajax({
            headers: {
                'X-CSRF-Token': CSRF_TOKEN 
            },
            url:"{{ URL::to('admin/orders/assign') }}",
            method:'POST',
            data:{'bookId':bookId,'driver_id':did},
            dataType:"json",
            success:function(data){
                if (data == 1) {
                    location.reload();
                }
            },error:function(data){
               
            }
        });
    }
</script>
@endsection