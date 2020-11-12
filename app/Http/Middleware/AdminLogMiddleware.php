<?php


namespace App\Http\Middleware;
use Closure;

class AdminLogMiddleware
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

        //写入管理员操作日志
        services()->adminLogService->create($request);

        return $next($request);
    }
}
