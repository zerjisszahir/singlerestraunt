@include('front.theme.header')

<section class="product-prev-sec product-list-sec">
    <div class="container">
        <div class="product-rev-wrap">
            <div class="cat-product search-product">
                <div class="cart-pro-head">
                    <h2 class="sec-head">Our Quality Food</h2>
                    <div class="btn-wrap" data-toggle="buttons">
                        <label id="list" class="btn">
                            <input type="radio" name="layout" id="layout1"> <i class="fas fa-list"></i>
                        </label>
                        <label id="grid" class="btn active">
                            <input type="radio" name="layout" id="layout2" checked> <i class="fas fa-th"></i>
                        </label>
                    </div>
                </div>
                <div class="row">
                    @foreach ($getitem as $item)
                    <div class="col-xl-4 col-md-6">
                        <div class="pro-box">
                            <div class="pro-img">
                                <a href="{{URL::to('product-details/'.$item->id)}}">
                                    <img src='{{$item["itemimage"]->image }}' alt="">
                                </a>
                                @if (Session::get('id'))
                                    @if ($item->is_favorite == 1)
                                        <i class="fas fa-heart i"></i>
                                    @else
                                        <i class="fal fa-heart i" onclick="MakeFavorite('{{$item->id}}','{{Session::get('id')}}')"></i>
                                    @endif
                                @endif
                            </div>
                            <div class="product-details-wrap">
                                <div class="product-details">
                                    <a href="{{URL::to('product-details/'.$item->id)}}">
                                        <h4>{{$item->item_name}}</h4>
                                    </a>
                                    <p class="pro-pricing">{{$getdata->currency}}{{number_format($item->item_price, 2)}}</p>
                                </div>
                                <div class="product-details">
                                    <p>{{ Str::limit($item->item_description, 60) }}</p>
                                    <!-- @if (Session::get('id'))
                                        <button class="btn" onclick="AddtoCart('{{$item->id}}','{{Session::get('id')}}')">Add to Cart</button>
                                    @else
                                        <a class="btn" href="{{URL::to('/signin')}}">Add to Cart</a>
                                    @endif -->
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                {!! $getitem->appends(request()->input())->links() !!}
            </div>
        </div>
    </div>
</section>

@include('front.theme.footer')