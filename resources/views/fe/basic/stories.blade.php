<section class="section-blogs">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-6">
                @if(count($data)>0)
                    @foreach($data as $story)
                    <div class="card shadow blog">
                        <div class="card-body">
                            <div>
                                <div class="pull-right">
                                    <span class="blog-date shadow-common">
                                            <span class="fa fa-calendar-o icon"></span>
                                            <span class="text">{{getFormattedDate($story->created_at)}}
                                            </span>
                                    </span>
                                </div>
                                <h2><a href="{{htcms_get_path($story->category_link_rewrite.'/'.$story->link_rewrite)}}">{{$story->title}}</a></h2>
                            </div>
                            {!!  $story->description !!}

                            <p class="more">
                                <a href="{{htcms_get_path($story->category_link_rewrite.'/'.$story->link_rewrite)}}">Read More</a>
                            </p>
                        </div>
                        <div class="card-footer">
                            <div class="pull-left author">
                                Author: {{$story->user_name}}
                            </div>
                            <div class="pull-right">
                                <span class="fa fa-comment-o"></span> {{$story->comments_count ?? 0}} Comments
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="card shadow blog">
                        <div class="card-body">
                            There is no blog post for now.
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-lg-3 col-6">
                <div style="border: 1px solid #c3c3c3; background: #c3c3c3; width: 350px; height: 250px">
                    Add Content
                </div>
            </div>
        </div>
    </div>
</section>
