<section class="sample-module">
    <div class="container">
            <h2>UrlService Module</h2>
        <p>
            This is a UrlSerivce module. Sometime you need to call a service based on query parameter. Below URL will be called with your limit param.
        </p>
        <p>
            <strong>Service URL:</strong>  https://picsum.photos/v2/list?page=2&limit=:limit
        </p>
                <div class="mb-5">
                    <h3>Total: {{sizeof($data)}}</h3>
                    Try to change url: such as {{htcms_get_domain_path()}}/example?limit=10
                </div>
                <div class="row">
                    @foreach($data as $pic)
                        <div class="col-lg-3 mb-5">
                            <img height="100" src="{{$pic['download_url']}}">
                            {{$pic['author']}}
                        </div>
                    @endforeach
                </div>
    </div>
</section>
