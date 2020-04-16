<?php

namespace App\Http\Controllers\User;


use App\Entities\Menu;
use App\Entities\MenusClass;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends BaseController
{
    //
    public function getMenu(Request $request)
    {
        //
        $class         = MenusClass::orderBy("sort", "asc")->get();
        $menu          = DB::table('menus')->leftJoin('menus_class', 'menus_class.id', '=', 'menus.class_id')
            ->select("menus.*", "menus_class.class_name", "menus_class.class_name_en")
            ->where("menus.status", 1)
            ->orderBy("menus.sort")
            ->get();
        $list["class"] = $class;
        $list["menu"]  = $menu;
        return $this->returnArray(200, '', $list);
    }

    public function editMenu(Request $request)
    {
        $data = $request->only(["id", "class_id", "name", "money", "img"]);

        $menuModel = new Menu();
        $menuModel->vaild_menu($data);


        $menu = Menu::where("id", $data["id"])->first();
        if ($menu == null) {
            throw new StoreResourceFailedException("不存在该菜品哦~");
        }

        $menu->class_id = $data['class_id'];
        $menu->name     = $data['name'];
        $menu->money    = $data['money'];
        $menu->img      = $data['img'];
        $menu->save();

        return $this->response->array(['status_code' => 200, 'message' => '菜品编辑成功~']);


    }

    public function addMenu(Request $request)
    {
        $data = $request->only(["class_id", "name", "money", "img"]);

        $menuModel = new Menu();
        $menuModel->vaild_menu($data);

        $menu_id = DB::table('menus')->insertGetId($data);

        if ($menu_id) {

            return $this->response->array(['status_code' => 200, 'message' => '菜品添加成功~']);
        } else {
            throw new StoreResourceFailedException('注册失败', ['mobile' => ['数据写入出错']]);
        }

    }


}
