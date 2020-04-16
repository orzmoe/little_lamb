<?php
/**
 * Created by PhpStorm.
 * User: poxiao
 * Date: 2020/4/2
 * Time: 17:07
 */

namespace App\Http\Controllers\Manager;

use App\Entities\Admin;
use App\Http\Controllers\User\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends BaseController
{


    public function list(Request $request)
    {
        $data = $request->only('pageSize');
        $list = DB::table('order')
            ->paginate($data['pageSize']);
        foreach ($list as $v) {
            $v->menu = DB::table('order_info')->where('order_info.oid', '=', $v->id)
                ->leftJoin('menus', 'menus.id', '=', 'order_info.mid')
                ->leftJoin('menus_class', 'menus_class.id', '=', 'menus.class_id')
                ->select( "order_info.num", "order_info.money as order_info_money", "menus.img", "menus.name as menus_name",
                    "menus_class.class_name")->get();
        }
        return $this->returnArray(200, '', $list);
    }
}