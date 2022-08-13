@include('front.theme.header')>

<section class="order-details">
    <div class="container">
        <h2 class="sec-head">Order Detail</h2>
        <p>({{$summery['order_number']}} - {{$summery['created_at']}})</p>
        @if($summery['order_type'] == 1)
            @if($summery['status'] == 1)
                <ul class="progressbar">
                    <li class="active">Order placed</li>
                    <li>Order ready</li>
                    <li>Order on the way</li>
                    <li>Order delivered</li>
                </ul>
            @elseif($summery['status'] == 2)
                <ul class="progressbar">
                    <li class="active">Order placed</li>
                    <li class="active">Order ready</li>
                    <li>Order on the way</li>
                    <li>Order delivered</li>
                </ul>
            @elseif($summery['status'] == 3)
                <ul class="progressbar">
                    <li class="active">Order placed</li>
                    <li class="active">Order ready</li>
                    <li class="active">Order on the way</li>
                    <li>Order delivered</li>
                </ul>
            @elseif($summery['status'] == 4)
                <ul class="progressbar">
                    <li class="active">Order placed</li>
                    <li class="active">Order ready</li>
                    <li class="active">Order on the way</li>
                    <li class="active">Order delivered</li>
                </ul>
            @elseif($summery['status'] == 5)
                <ul class="progressbar">
                    <li class="active">Order Cancelled by You</li>
                    <li>Order ready</li>
                    <li>Order on the way</li>
                    <li>Order delivered</li>
                </ul>
            @elseif($summery['status'] == 6)
                <ul class="progressbar">
                    <li class="active">Order Cancelled by Admin</li>
                    <li>Order ready</li>
                    <li>Order on the way</li>
                    <li>Order delivered</li>
                </ul>
            @endif
        @else
            @if($summery['status'] == 1)
                <ul class="progressbar" style="text-align: center;">
                    <li class="active">Order placed</li>
                    <li>Order ready</li>
                    <li>Order delivered</li>
                </ul>
            @elseif($summery['status'] == 2)
                <ul class="progressbar" style="text-align: center;">
                    <li class="active">Order placed</li>
                    <li class="active">Order ready</li>
                    <li>Order delivered</li>
                </ul>
            @elseif($summery['status'] == 4)
                <ul class="progressbar" style="text-align: center;">
                    <li class="active">Order placed</li>
                    <li class="active">Order ready</li>
                    <li class="active">Order delivered</li>
                </ul>
            @elseif($summery['status'] == 5)
                <ul class="progressbar" style="text-align: center;">
                    <li class="active">Order Cancelled by You</li>
                    <li>Order ready</li>
                    <li>Order delivered</li>
                </ul>
            @elseif($summery['status'] == 6)
                <ul class="progressbar" style="text-align: center;">
                    <li class="active">Order Cancelled by Admin</li>
                    <li>Order ready</li>
                    <li>Order delivered</li>
                </ul>
            @endif
        @endif
        <div class="row">
            <div class="col-lg-8">
                @foreach ($orderdata as $orders)
                <div class="order-details-box">
                    <div class="order-details-img">
                        <img src='{{$orders["itemimage"]->image }}' alt="">
                    </div>
                    <div class="order-details-name">
                        <a href="javascript:void(0)">
                            <a href="{{URL::to('product-details/'.$orders->id)}}">
                                <h3>{{$orders->item_name}} <span>{{$getdata->currency}}{{number_format($orders->total_price, 2)}}</span></h3>
                            </a>
                        </a>
                        <p>Qty : {{$orders->qty}}</p>

                        @foreach ($orders['addons'] as $addons)
                        <div class="cart-addons-wrap">
                            <div class="cart-addons">
                                <b>{{$addons['name']}}</b> : {{$getdata->currency}}{{number_format($addons['price'], 2)}}
                            </div>
                        </div>
                        @endforeach

                        @if ($orders->item_notes != "")
                            <p class="cart-pro-note">{{$orders->item_notes}}</p>
                        @endif
                        
                    </div>
                </div>
                <?php
                    $data[] = array(
                        "total_price" => $orders->total_price
                    );
                ?>
                @endforeach
            </div>
            <div class="col-lg-4">
                <div class="order-payment-summary">
                    <h3>Payment summary</h3>
                    <p>Order total <span>{{$getdata->currency}}{{number_format(array_sum(array_column(@$data, 'total_price')), 2)}}</span></p>
                    
                    <p>Tax ({{$summery['tax']}}%) <span>{{$getdata->currency}}{{number_format($summery['tax_amount'], 2)}}</span></p>

                    @if($summery['delivery_charge'] != "0")
                    
                    <p>Delivery charge <span>{{$getdata->currency}}{{number_format(@$summery['delivery_charge'], 2)}}</span></p>

                    @endif

                    @if ($summery['promocode'] !="")
                    <p>Discount ({{$summery['promocode']}}) <span>- {{$getdata->currency}}{{number_format($summery['discount_amount'], 2)}}</span></p>
                    @endif
                    <?php
                    $a = array_sum(array_column(@$data, 'total_price'));
                    $b = array_sum(array_column(@$data, 'total_price'))*$summery['tax']/100;
                    $c = $summery['delivery_charge'];
                    $d = $summery['discount_amount'];
                    
                    if ($d == "NaN") {
                        $total = $a+$b+$c;
                    } else {
                        $total = $a+$b+$c-$d;
                    }
                    
                    ?>
                    <p class="order-details-total">Total Amount <span>{{$getdata->currency}}{{number_format($total, 2)}}</span></p>
                </div>

                @if($summery['driver_name'] != "")
                
                    <div class="order-add">
                        <h6>Driver information</h6>
                            <div class="order-details-img">
                                <img src='{{$summery["driver_profile_image"]}}' alt="">
                            </div>
                        <p class="mt-3">{{$summery['driver_name']}}</p>
                        <p>
                            <a href="tel:{{$summery['driver_mobile']}}"> {{$summery['driver_mobile']}}</a>
                        </p>
                    </div>

                @endif

                @if($summery['order_type'] == 1)
                
                    <div class="order-add">
                        <h6>Delivery address</h6>
                        <p>{{$summery['address']}}</p>
                        <h6>Door / Flat no.</h6>
                        <p>{{$summery['building']}}</p>
                        <h6>Landmark</h6>
                        <p>{{$summery['landmark']}}</p>
                        <h6>Pincode</h6>
                        <p>{{$summery['pincode']}}</p>
                    </div>

                @endif
                
                @if ($summery['order_notes'] !="")
                <div class="order-add">
                    <h6>Notes</h6>
                    <p>{{$summery['order_notes']}}</p>
                </div>
                @endif

                @if ($summery['status'] == 1)
                <div class="delivery-btn-wrap">
                    <button type="button" class="btn open comman" onclick="OrderCancel('{{$summery['id']}}')">Cancel order</button>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@include('front.theme.footer')