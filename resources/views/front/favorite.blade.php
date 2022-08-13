@include('front.theme.header')



<section class="favourite">

    <div class="container">

        <h2 class="sec-head">Favourite List</h2>

        <div class="row">

            @if (count($favorite) == 0)

                <p>No Data found</p>

            @else 

                @foreach ($favorite as $item)

                <div class="col-lg-4 col-md-6">

                    <div class="pro-box">

                        <div class="pro-img">

                            <a href="{{URL::to('product-details/'.$item->id)}}">

                                <img src='{{$item["itemimage"]->image }}' alt="">

                            </a>

                            <i class="fas fa-heart i" onclick="Unfavorite('{{$item->id}}','{{Session::get('id')}}')"></i>

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

            @endif

        </div>

        {!! $favorite->links() !!}

    </div>

</section>



@include('front.theme.footer')