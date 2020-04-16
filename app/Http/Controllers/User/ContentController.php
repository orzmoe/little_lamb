<?php

namespace App\Http\Controllers\User;


use App\Entities\Content;
use App\Entities\Menu;
use App\Http\Controllers\User\BaseController;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentController extends BaseController
{

    public function list(Request $request)
    {
        $data = $request->only(['num', 'lang']);
        isset($data['num']) ? $num = $data['num'] : $num = 100;
        $list = DB::table('content')
            ->where("status", 1)
            ->where("lang", $data['lang'])
            ->limit($num)
            ->orderByDesc("id")
            ->get();
        foreach ($list as $v) {
            $v->time = date("F d,Y", strtotime($v->created_at));
        }
        return $this->returnArray(200, '', $list);
    }

    public function info(Request $request)
    {
        $data = $request->only('id');
        $info = DB::table('content')
            ->where("status", 1)
            ->where("id", $data['id'])
            ->first();
        $info->time = date("F d,Y", strtotime($info->created_at));
        return $this->returnArray(200, '', $info);
    }

}
