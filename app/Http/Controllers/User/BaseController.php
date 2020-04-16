<?php

namespace App\Http\Controllers\User;

use Dingo\Api\Routing\Helpers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends Controller
{
    use Helpers;

    public function __construct()
    {
        app('Dingo\Api\Exception\Handler')->register(function (ModelNotFoundException $exception) {
            throw new NotFoundHttpException('resource not found');
        });
    }

    public function returnArray($status_code = 200, $message = "", $data = [])
    {
        return $this->response->array(['status_code' => $status_code, 'message' => $message, "data" => $data]);
    }

    public function generateCode($buyer_uid)
    {
        $arr       = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $y         = date('y');
        $m         = $arr[date('n') - 1];
        $d         = date('d');
        $h         = $arr[date('G')];
        $timeStamp = $y . $m . $d . $h . date('is');

        $uid  = sprintf('%07d', (int)$buyer_uid);
        $rand = sprintf('%03d', mt_rand(0, 999));
        return $timeStamp . $uid . $rand;
    }


    public function setUserToken($userInfo)
    {
        $token = md5(uniqid(mt_rand(), true));
        Cache::put('Manager:Token:' . $token, $userInfo, 86400);
        return $token;
    }
}
