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
            <span id="message"></span>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{$paymentdetails->payment_name}}</h4>
                    <div class="basic-form">
                        <form action="{{ URL::to('admin/payment/update') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label>Environment</label>
                                <select id="environment" name="environment" class="form-control">
                                    <option selected="selected" value="">Choose...</option>
                                    <option value="0" {{$paymentdetails->environment == 0  ? 'selected' : ''}}>Production</option>
                                    <option value="1" {{$paymentdetails->environment == 1  ? 'selected' : ''}}>Sandbox</option>
                                </select>
                            </div>

                            <input type="hidden" name="id" class="form-control" value="{{$paymentdetails->id}}">

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    @if($paymentdetails->payment_name == "Stripe")
                                    <label>
                                            Stripe Sendbox Public Key
                                    </label>
                                    <input type="text" name="test_public_key" class="form-control" placeholder="Stripe Sendbox Public Key" value="{{$paymentdetails->test_public_key}}">
                                    @else 
                                    <label>
                                            RazorPay Sendbox Public Key
                                    </label>
                                    <input type="text" name="test_public_key" class="form-control" placeholder="RazorPay Sendbox Public Key" value="{{$paymentdetails->test_public_key}}">
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    @if($paymentdetails->payment_name == "Stripe")
                                    <label>
                                            Stripe Sendbox Secret Key
                                    </label>
                                    <input type="text" name="test_secret_key" class="form-control" placeholder="Stripe Sendbox Secret Key" value="{{$paymentdetails->test_secret_key}}">
                                    @else 
                                    <label>
                                            RazorPay Sendbox Secret Key
                                    </label>
                                    <input type="text" name="test_secret_key" class="form-control" placeholder="RazorPay Sendbox Secret Key" value="{{$paymentdetails->test_secret_key}}">
                                    @endif
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">

                                    @if($paymentdetails->payment_name == "Stripe")
                                    <label>
                                            Stripe Production Public Key
                                    </label>
                                    <input type="text" name="live_public_key" class="form-control" placeholder="Stripe Production Public Key" value="{{$paymentdetails->live_public_key}}">
                                    @else 
                                    <label>
                                            RazorPay Production Public Key
                                    </label>
                                    <input type="text" name="live_public_key" class="form-control" placeholder="RazorPay Production Public Key" value="{{$paymentdetails->live_public_key}}">
                                    @endif

                                </div>
                                <div class="form-group col-md-6">

                                    @if($paymentdetails->payment_name == "Stripe")
                                    <label>
                                            Stripe Production Secret Key
                                    </label>
                                    <input type="text" name="live_secret_key" class="form-control" placeholder="Stripe Production Secret Key" value="{{$paymentdetails->live_secret_key}}">
                                    @else 
                                    <label>
                                            RazorPay Production Secret Key
                                    </label>
                                    <input type="text" name="live_secret_key" class="form-control" placeholder="RazorPay Production Secret Key" value="{{$paymentdetails->live_secret_key}}">
                                    @endif
                                </div>
                            </div>
                            
                            <!-- <div class="form-group">
                                <label>Currency code</label>
                                <input type="text" class="form-control" placeholder="Enter your Currency for Payment">
                            </div> -->
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- #/ container -->
@endsection
@section('script')

@endsection