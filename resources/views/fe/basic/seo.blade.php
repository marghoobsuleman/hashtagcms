<section class="section-faq">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="heading">{{____("hashtagcms::modules.Frequently asked questions")}}</h2>
        </div>
        <div class="row">

            <div class="col-md-6">
                <h4 class="faq-title">{{____("hashtagcms::modules.Is it Free?")}}</h4>
                <p class="faq-text">
                    {{____("hashtagcms::modules.Yes, It's MIT license. You can ask for other license.")}}
                </p>

                <h4 class="faq-title">{{____("hashtagcms::modules.If there is any bug?")}}</h4>
                <p class="faq-text">
                    {!! ____("hashtagcms::modules.bug_post") !!}

                </p>
                <h4 class="faq-title">{{____("hashtagcms::modules.Professional Help?")}}</h4>
                <p class="faq-text">
                    {{____("hashtagcms::modules.Of course, we provide professional consultants.")}}
                    <a href='{{htcms_get_path('contact')}}'>{{____("hashtagcms::modules.Ask for price")}}</a>.
                </p>
            </div>

            <div class="col-md-6">
                <h4 class="faq-title">{{____("hashtagcms::modules.Would you like to sponser #CMS?")}}</h4>
                <p class="faq-text">
                    {{____('hashtagcms::modules.Thank you! You are so kind. You can; by scanning below QR code. Please mention in comment "Support for HashtagCMS".')}}
                    <br />
                    <img height="270" src="{{htcms_get_image_resource('qr-code.jpg')}}">
                </p>
            </div>

        </div>
    </div>
</section> <!-- end faq -->