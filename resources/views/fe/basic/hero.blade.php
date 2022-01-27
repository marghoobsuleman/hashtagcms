<!-- slider_area_start -->
@php
    $rnm = rand ( 1 , 2);
@endphp
<section class="hero" id="hero">
    <div class="hero-inner hero-bg-{{$rnm}}">
        <div class="container">
            <div>
                <div class="hero-text">
                    {!! ____("hashtagcms::modules.hero_text_1") !!}
                </div>
                <div></div>
                <div class="hero-text">
                    {!! ____("hashtagcms::modules.hero_text_2") !!}
                </div>
            </div>

        </div>
    </div>
</section>
