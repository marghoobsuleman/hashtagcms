@foreach($data as $social)
    @php
        $sLabel = ucfirst($social->name);
        $sCss = strtolower($social->name);
        $sHref = trim($social->value);
    @endphp
    <a class="social social-{{$sCss}}" href="{{$sHref}}" title="{{$sLabel}}" target="_blank" rel="noopener nofollow"><i class="fa fa-{{$sCss}}"></i></a>
@endforeach