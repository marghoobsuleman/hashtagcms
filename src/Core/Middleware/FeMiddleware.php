<?php

namespace MarghoobSuleman\HashtagCms\Core\Middleware;

use MarghoobSuleman\HashtagCms\Core\Middleware\Traits\BaseInfo;

use Closure;


/**
 * Frontend Interceptor Middleware
 * Registered as 'interceptor' => MarghoobSuleman\HashtagCms\Http\Middleware\FeMiddleware::class
 */


class FeMiddleware
{

    use BaseInfo;

    protected $request;


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->request = $request;

        info("================== FeMiddleware: [".$request->path()."] ====================");
        $this->process($this->request);
        info("================== End============================================");
        
        return $next($this->request);
    }

    /**
     * Check if request should be processed
     * @param $path
     * @return bool
     */
    private function shouldProcess($path) {

        //Handle something special
        return true;

    }

    private function process($request) {

        $path = $request->path();

        if($this->shouldProcess($path)) {

            $this->setBaseInfo($request);

        }

    }



}
