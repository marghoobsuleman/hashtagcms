<section class="sample-module">
    <div class="container">
            <h2>Service Module</h2>
        <p>
            This is a service module. It returns data from a service.
        </p>
        <p>
            <strong>Service URL:</strong>  https://picsum.photos/v2/list?limit=4
        </p>
                <div class="row">
                    @foreach($data as $pic)
                    <div class="col-lg-5 mb-5">
                        <img width="300" src="{{$pic['download_url']}}">
                        {{$pic['author']}}
                    </div>
                    @endforeach
                </div>
    </div>
</section>
