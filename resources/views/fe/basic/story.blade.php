
<section class="section-blogs">
    <div class="container">
        @if(isset($data) && count($data) > 0)
            @php
                $data = $data[0];
                $hasContent = true;
                $categoryId = $data->category_id;
                $contentId = $data->id;

            @endphp
            {!! $data->page_content !!}
        @else
            @php
                $hasContent = false;
            @endphp

        @endif
    </div>
</section>

@php
    $user = auth()->user();
    if($user != null) {
        $name = $user->name. " ".$user->last_name;
        $email = $user->email;
    } else {
        $name = "";
        $email = "";
    }

@endphp


@if($hasContent)
    @if($data->enable_comments == 1)
        <section class="section-comments">
            <div class="container">
                <h2>Post Comments</h2>
                <comment-box
                    data-name="{{$name}}"
                    data-email="{{$email}}"
                    data-category-id="{{$categoryId}}"
                    data-content-id="{{$contentId}}">
                </comment-box>

                <comments-list
                    data-comments-count="{{$data->comments_count}}"
                    data-category-id="{{$categoryId}}"
                    data-content-id="{{$contentId}}"
                    data-image-size="70"
                    data-no-comment-available-message="Be the first to write a comment."
                    data-default-image="{{htcms_get_image_resource('user-default.jpg')}}"
                    data-auto-load="true"
                    data-display-order="desc"
                >
                </comments-list>
            </div>
        </section>
    @endif
@endif
