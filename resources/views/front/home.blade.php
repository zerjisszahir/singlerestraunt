@include('front.theme.header')

<section class="banner-sec">
    <div class="container-fluid px-0">
        <div class="banner-carousel owl-carousel owl-theme">
            @foreach ($getslider as $slider)
            <div class="item">
                <img src='{!! asset("public/images/slider/".$slider->image) !!}' alt="">
                <div class="banner-contant">
                    <h1>{{$slider->title}}</h1>
                    <p>{{$slider->description}}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="feature-sec">
    <div class="container">
        <div class="feature-carousel owl-carousel owl-theme">
            @foreach ($getbanner as $banner)
            <div class="item">
                <div class="feature-box">
                    <a href="{{URL::to('product-details/'.$banner->item_id)}}">
                        <img src='{!! asset("public/images/banner/".$banner->image) !!}' alt="">
                    </a>
                    <div class="feature-contant">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="product-prev-sec">
    <div class="container">
        <h2 class="sec-head">Our Products</h2>
        <div id="sync2" class="owl-carousel owl-theme">
            <?php $i=1; ?>
            @foreach ($getcategory as $category)
            <div class="item product-tab">
                <img src='{!! asset("public/images/category/".$category->image) !!}' alt=""> {{$category->category_name}}
            </div>
            <?php $i++; ?>
            @endforeach
        </div>
        <div id="sync1" class="owl-carousel owl-theme">
            <?php $i=1; ?>
            @foreach($getcategory as $category)
            <div class="item">
                <div class="tab-pane">
                    <div class="row">
                        <?php $count = 0; ?>
                        @foreach($getitem as $item)
                        @if($item->cat_id==$category->id)
                        <?php if($count == 6) break; ?>
                        <div class="col-lg-4 col-md-6">
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
                                    @else
                                        <a class="i" href="{{URL::to('/signin')}}"><i class="fal fa-heart"></i></a>
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
                        <?php $count++; ?>
                        @endif
                        @endforeach
                    </div>
                    <a href="{{URL::to('product/')}}" class="btn">View More</a>
                </div>
            </div>
            <?php $i++; ?>
            @endforeach
        </div>
    </div>
</section>

<section class="about-sec">
    <div class="container">
        <div class="about-box">
            <div class="about-img">
                <img src='{!! asset("public/images/about/".$getabout->image) !!}' alt="">
            </div>
            <div class="about-contant">
                <h2 class="sec-head text-left">About us</h2>
                <p>{!! \Illuminate\Support\Str::limit(htmlspecialchars($getabout->about_content, ENT_QUOTES, 'UTF-8'), $limit = 500, $end = '...') !!}</p>
            </div>
        </div>
    </div>
</section>

<section class="review-sec">
    <div class="container">
        <h2 class="sec-head">Our Reviews</h2>
        <div class="review-carousel owl-carousel owl-theme">
            @foreach($getreview as $review)
            <div class="item">
                <div class="review-profile">
                    <img src='{!! asset("public/images/profile/".$review["users"]->profile_image) !!}' alt="">
                </div>
                <h3>{{$review['users']->name}}</h3>
                <p>{{$review->comment}}</p>
            </div>
            @endforeach
        </div>

    </div>
</section>

<section class="our-app">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <h2 class="sec-head">Order food directly from your phone</h2>
                <p>Search for Restaurant App in the App Store and Google Play Store. <br>Enjoy your food on the go!</p>
            </div>
            <div class="col-lg-6">
                @if($getabout->ios != "")
                    <a href="{{$getabout->ios}}" class="our-app-icon" target="_blank">
                        <img src="{!! asset('public/front/images/apple-store.svg') !!}" alt="">
                    </a>
                @endif

                @if($getabout->android != "")
                    <a href="{{$getabout->android}}" class="our-app-icon" target="_blank">
                        <img src="{!! asset('public/front/images/play-store.png') !!}" alt="">
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="contact-from">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="sec-head">Contact us</h2>
                @if($getabout->mobile != "")
                    <a href="tel:{{$getabout->mobile}}" class="contact-box">
                        <i class="fas fa-phone-alt"></i>
                        <p>{{$getabout->mobile}}</p>
                    </a>
                @endif

                @if($getabout->email != "")
                    <a href="mailto:{{$getabout->email}}" class="contact-box">
                        <i class="fas fa-envelope"></i>
                        <p>{{$getabout->email}}</p>
                    </a>
                @endif

                @if($getabout->address != "")
                    <div class="contact-box">
                        <i class="fas fa-home"></i>
                        <p>{{$getabout->address}}</p>
                    </div>
                @endif
            </div>
            <div class="col-lg-6">
                <form class="contact-form" id="contactform" method="post">
                    {{csrf_field()}}
                    <input type="text" name="firstname" placeholder="First name*" id="firstname" required="">
                    <input type="text" name="lastname" placeholder="Last name*" id="lastname" required="">
                    <input type="email" name="email" placeholder="Email*" id="email" required="">
                    <textarea name="message" placeholder="Message" id="message" required=""></textarea>
                    <button type="button" name="submit" class="btn" onclick="contact()">Submit</button>
                </form>
            </div>
        </div>
    </div>
</section>

@include('front.theme.footer')