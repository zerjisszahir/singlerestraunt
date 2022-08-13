@include('front.theme.header')

<section class="favourite">
    <div class="container">
        <h2 class="sec-head">Privacy Policy</h2>
        <div class="row">
            {!!$getprivacypolicy->privacypolicy_content!!}
        </div>
    </div>
</section>

@include('front.theme.footer')