@include('front.theme.header')



<section class="product-details-sec">

    <div class="container">

        <div class="row">

            <div class="col-lg-5">

                <div class="product-details-img owl-carousel owl-theme">

                    @foreach ($getimages as $images)

                    <div class="item">

                        <a data-fancybox="gallery" href="{{$images->image }}">

                            <img src='{{$images->image }}' alt="">

                        </a>

                    </div>

                    @endforeach

                </div>

            </div>

            <div class="col-lg-7 pro-details-display">

                <div class="pro-details-name-wrap">

                    <h3 class="sec-head mt-0">{{$getitem->item_name}}</h3>

                    <input type="hidden" name="price" id="price" value="{{$getitem->item_price}}">

                    @if (Session::get('id'))

                        @if ($getitem->is_favorite == 1)

                            <i class="fas fa-heart i"></i>

                        @else

                            <i class="fal fa-heart i" onclick="MakeFavorite('{{$getitem->id}}','{{Session::get('id')}}')"></i>

                        @endif

                    @else

                        <a class="i" href="{{URL::to('/signin')}}"><i class="fal fa-heart i"></i></a>

                    @endif

                </div>

                <small>{{$getitem['category']->category_name}}</small>

                <div class="extra-food-wrap">

                    @if (count($freeaddons) == 0 && count($paidaddons) == 0)

                        No Add-ons found

                    @endif

                    @if (count($freeaddons) != 0)

                        <ul class="list-unstyled extra-food">

                            @if ($freeaddons != "")

                                <h3>Free Add-ons</h3>

                                @foreach ($freeaddons as $addons)

                                <li>

                                    <input type="checkbox" name="addons[]" class="Checkbox" value="{{$addons->id}}" price="{{$addons->price}}">

                                    <p>{{$addons->name}}</p>

                                </li>

                                @endforeach

                            @else



                            @endif

                        </ul>

                    @endif

                    @if (count($paidaddons) != 0)

                        <ul class="list-unstyled extra-food">

                            <h3>Paid Add-ons</h3>

                            <div id="pricelist">

                            @foreach ($paidaddons as $addons)

                            <li>

                                <input type="checkbox" name="addons[]" class="Checkbox" value="{{$addons->id}}" price="{{$addons->price}}">

                                <p>{{$addons->name}} : {{$getdata->currency}}{{number_format($addons->price, 2)}}</p>

                            </li>

                            @endforeach

                            </div>

                        </ul>

                    @endif



                    <div class="pro-details-add-wrap">

                        <p class="pricing">{{$getdata->currency}}{{number_format($getitem->item_price, 2)}}</p>

                        <p class="open-time"><i class="far fa-clock"></i> {{$getitem->delivery_time}}</p>

                        @if (Session::get('id'))

                            @if ($getitem->item_status == '1')

                                <button class="btn" onclick="AddtoCart('{{$getitem->id}}','{{Session::get('id')}}')">Add to cart</button>

                            @else 

                                <button class="btn" disabled="">Item currently unavailable</button>

                            @endif

                        @else

                            @if ($getitem->item_status == '1')

                                <a class="btn" href="{{URL::to('/signin')}}">Add to cart</a>

                            @else 

                                <button class="btn" disabled="">Item currently unavailable</button>

                            @endif

                        @endif

                    </div>

                </div>

                <textarea id="item_notes" name="item_notes" placeholder="Write Notes..."></textarea>

            </div>

            <div class="col-12">

                <h4 class="sec-head">Description</h4>

                <p>{{$getitem->item_description}}</p>



                @if($getingredients != "")

                <h4 class="sec-head">Ingredients</h4>

                    <div class="ingredients-carousel owl-carousel owl-theme">

                        @foreach ($getingredients as $ingredients)

                        <div class="item">

                            <div class="ingredients-box">

                                <img src='{{$ingredients->image }}' alt="">

                            </div>

                        </div>

                        @endforeach

                    </div>

                @else

                    <p class="no-data">No Data Ingredients</p>

                @endif

            </div>

            <div class="col-12">

                <h2 class="sec-head text-center">Related Food</h2>

                <div class="pro-ref-carousel owl-carousel owl-theme">

                    @foreach($relatedproduct as $item)

                    <div class="item">

                        <div class="pro-box">

                            <div class="pro-img">

                                <a href="{{URL::to('product-details/'.$item->id)}}">

                                    <img src='{{$item["itemimage"]->image }}' alt="">

                                </a>

                                @if (Session::get('id'))

                                    @if ($item->is_favorite == 1)

                                        <i class="fas fa-heart i"></i>

                                    @else

                                        <i class="fal fa-heart i"  onclick="MakeFavorite('{{$item->id}}','{{Session::get('id')}}')"></i>

                                    @endif

                                @else

                                    <a href="{{URL::to('/signin')}}"><i class="fal fa-heart i"></i></a>

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

                                </div>

                            </div>

                        </div>

                    </div>

                    @endforeach

                </div>



            </div>

        </div>

    </div>

</section>



@include('front.theme.footer')

<script type="text/javascript">

var total = parseFloat($("#price").val()); 



$('input[type="checkbox"]').change(function() {

    if($(this).is(':checked')){

        total += parseFloat($(this).attr('price')) || 0;

    }

    else{

        total -= parseFloat($(this).attr('price')) || 0;

    }

$('p.pricing').text('{{$getdata->currency}}'+total.toFixed(2));

$('#price').val(total.toFixed(2));

})



</script>