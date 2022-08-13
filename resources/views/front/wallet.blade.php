@include('front.theme.header')>

<section class="order-details">
    <div class="container">
        <h2 class="sec-head">My Wallet</h2>

        <div class="row mt-5">
            <div class="col-lg-4">
                <div class="order-payment-summary" style="background-color: #fd3b2f">
                    <div class="col-4 mx-auto text-center">
                        <img src='{!! asset("public/front/images/ic_wallet.png") !!}' width="100px" alt="" class="text-center">
                    </div>
                    
                    <h2 class="text-center mt-3">Wallet Balance</h2>
                    <h1 class="text-center" style="color: #fff;"><span>{{$getdata->currency}}{{number_format($walletamount->wallet, 2)}}</span></h1>
                </div>
            </div>
            <div class="col-lg-8">
                @foreach ($transaction_data as $orders)
                    @if ($orders->transaction_type == 1)
                    <div class="order-details-box">
                        <div class="wallet-details-img">
                            <img src='{!! asset("public/front/images/ic_trGreen.png") !!}' alt="" class="mt-1">
                        </div>
                        <div class="order-details-name mt-3">
                            <h3> {{$orders->order_number}} <span style="color: #000;">{{$orders->date}}</span></h3>
                            <h3><span style="color: #ff0000;">Order Cancelled</span> <span style="color: #00c56a;"> {{$getdata->currency}}{{number_format($orders->wallet, 2)}}</span></h3>
                        </div>
                    </div>
                    @elseif ($orders->transaction_type == 2)

                    <div class="order-details-box">
                        <div class="wallet-details-img">
                            <img src='{!! asset("public/front/images/ic_trRed.png") !!}' alt="" class="mt-1">
                        </div>
                        <div class="order-details-name mt-3">
                            <h3> {{$orders->order_number}} <span style="color: #000;">{{$orders->date}}</span></h3>
                            <h3><span style="color: #00c56a;">Order Confirmed</span> <span style="color: #ff0000;"> - {{$getdata->currency}}{{number_format($orders->wallet, 2)}}</span></h3>
                        </div>
                    </div>

                    @elseif ($orders->transaction_type == 3)

                        <div class="order-details-box">
                            <div class="wallet-details-img">
                                <img src='{!! asset("public/front/images/ic_trGreen.png") !!}' alt="" class="mt-1">
                            </div>
                            <div class="order-details-name mt-3">
                                <a href="javascript:void(0)">
                                    <a href="#">
                                        <h3> {{$orders->username}} <span style="color: #000;">{{$orders->date}}</span></h3>
                                    </a>
                                </a>
                                <h3><span style="color: #00c56a;">Referral Earning</span> <span style="color: #00c56a;">{{$getdata->currency}}{{number_format($orders->wallet, 2)}}</span></h3>
                            </div>
                        </div>

                    @endif
                @endforeach
                {!! $transaction_data->links() !!}
            </div>
        </div>
    </div>
</section>

@include('front.theme.footer')