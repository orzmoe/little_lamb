<?php

namespace App\Http\Middleware\Manager;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Dingo\Api\Exception\StoreResourceFailedException;

class loginCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->json()->get('status') == null) {
            $request->json()->remove("status");
        }
        if ($request->input("token")) {
            $token = $request->input("token");
        } else {
            $token = $request->header("Token");
        }

        $request['userInfo'] = json_decode($this->loginCheck($token), 1);
        return $next($request);
    }

    public function loginCheck($token)
    {

        $userInfo = Cache::Get('Manager:Token:' . $token);

        if ($userInfo) {
            Cache::put('Manager:Token:' . $token, $userInfo, 86400);
            return $userInfo;
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('', '请重新登陆');
        }
    }
}
