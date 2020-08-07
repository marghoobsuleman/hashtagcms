<section class="sample-module">
    <div class="container">
            <h2>QueryService Module</h2>
        <p>
            This is a combination of service and query module. Sometime we need data from service and query both. <br />
            In this case data has two properties. ie. serviceData, queryData
        </p>
        <p>
            <strong>Service URL:</strong>  https://picsum.photos/v2/list?page=2&limit=4
        </p>
        <p>
            <strong>Query:</strong>  select * from site_props where site_id=:site_id and group_name="SocialLinks" and is_public=1 and deleted_at is null;
        </p>
        <h3>Service Data</h3>
        <div class="row">
            @php
                $serviceData = $data['serviceData'];
                $queryData = $data['queryData'];
            @endphp
            @foreach($serviceData as $pic)
                <div class="col-lg-5 mb-5">
                    <img height="150" src="{{$pic['download_url']}}">
                    {{$pic['author']}}
                </div>
            @endforeach
        </div>
        <div class="row">
            <h3 class="ml-1">Query Data</h3>
        </div>
        <div class="row">
            @foreach($queryData as $social)
                @php
                    $sLabel = ucfirst($social->name);
                    $sCss = strtolower($social->name);
                    $sHref = trim($social->value);
                @endphp
                <p class="p-3"><a class="social social-{{$sCss}}" href="{{$sHref}}" title="{{$sLabel}}" target="_blank" rel="noopener nofollow"><i class="fa fa-{{$sCss}}"></i> {{$sLabel}}</a> &nbsp;</p>
            @endforeach
        </div>
    </div>
</section>
