<?php
namespace MarghoobSuleman\HashtagCms\Core\Middleware\Admin;

use Closure;


class BeMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if ($user!=null && $user->user_type == "Visitor") {

            abort(403);
        }

        $result =  $next($request);

        //info("cmsInterceptor: Moving to Next middleware");

        return  $result;
    }


}
