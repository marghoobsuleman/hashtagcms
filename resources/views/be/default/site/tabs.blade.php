<ul class="nav nav-tabs margin-top-10" style="margin-left:20px">

    @foreach($tabs as $key=>$tab)
        @php
            $currentTab = str_replace(" ", "", strtolower($tab));
            $activeTab = str_replace(" ", "", strtolower($activeTab));
            $href = htcms_admin_path("site/settings/{$siteInfo->id}/$currentTab");
        @endphp
        @if($currentTab == $activeTab)
            @php
                $css = "active";
            @endphp
        @else
            @php
                $css = "";
            @endphp
        @endif

        <li role="presentation" class="{{$css}}"><a href="{{$href}}">{{$tab}}</a></li>
    @endforeach
</ul>
