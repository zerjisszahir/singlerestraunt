<div class="row mt-5">
    <div class="col-lg-3 col-sm-6">
        <div class="card gradient-1">
            <a href="#" style="text-decoration: none;">
                <div class="card-body">
                    <h3 class="card-title text-white">Total Orders</h3>
                    <div class="d-inline-block">
                        <h2 class="text-white">{{@$total_order}}</h2>
                    </div>
                    <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-bar-chart"></i></span>
                </div>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card gradient-2">
            <a href="#" style="text-decoration: none;">
                <div class="card-body">
                    <h3 class="card-title text-white">Cancelled order</h3>
                    <div class="d-inline-block">
                        <h2 class="text-white">{{@$canceled_order}}</h2>
                    </div>
                    <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-shopping-cart"></i></span>
                </div>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card gradient-3">
            <a href="#" style="text-decoration: none;">
                <div class="card-body">
                    <h3 class="card-title text-white">Total Earnings</h3>
                    <div class="d-inline-block">
                        <h2 class="text-white">{{Auth::user()->currency}}{{ number_format(@$order_total, 2) }}</h2>
                    </div>
                    <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-usd"></i></span>
                </div>
            </a>
        </div>
    </div>

</div>
<table id="example" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>User Name</th>
            <th>Order Number</th>
            <th>Address</th>
            <th>Payment Type</th>
            <th>Payment ID</th>
            <th>Total Amount</th>
            <th>Order Status</th>
            <th>Order Assigned To</th>
            <th>Created at</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($getorders as $orders) {
        ?>
        <tr id="dataid{{$orders->id}}">
            <td>{{$i}}</td>
            <td>{{$orders['users']->name}}</td>
            <td>{{$orders->order_number}}</td>
            <td>{{$orders->address}}</td>
            <td>
                @if($orders->payment_type =='0')
                      COD
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
            <td>{{$orders->order_total}} IQD</td>
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
        </tr>
        <?php
        $i++;
        }
        ?>
    </tbody>
</table>