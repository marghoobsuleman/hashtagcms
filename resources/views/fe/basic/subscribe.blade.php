<section class="section-subscribe">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <h2 class="newsletter-title">Subscribe to our newsletter</h2>
                <p class="newsletter-subtitle">Please leave us your email address. We will update you.</p>
                <form data-form="subscribe-form" class="newsletter-form relative subscribe-form" action="/common/subscribe" method="post" onsubmit="return HashtagCms.Subscribe.subscribeNow(this)">
                    <div class="row">
                        <div class="col-8" style="padding: 0; margin: 0">
                                <input type="email" name="email" class="form-control input " placeholder="Email address" required>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-lg btn-info btn-block subscribe" type="submit">Submit</button>
                        </div>
                        <div class="alert col-12 mt-2" data-message-holder="subscribe-message-holder" style="display: none; background-color: lightyellow">
                            <span class="message" data-class="subscribe-message"></span> <span data-class="subscribe-close" title="Close" class="pull-right pointer hand" style="background-color: #5f5f5f; color:#fff;padding:1px 5px">x</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section> <!-- end Newsletter -->
