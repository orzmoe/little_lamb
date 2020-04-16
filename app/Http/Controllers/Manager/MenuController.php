<?php

namespace App\Http\Controllers\Manager;


use App\Entities\Menu;
use App\Entities\MenusClass;
use App\Http\Controllers\User\BaseController;
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
            ->orderBy("sort")
            ->get();
        $list["class"] = $class;
        $list["menu"]  = $menu;
        return $this->returnArray(200, '', $list);
    }

    public function editMenu(Request $request)
    {
        $data = $request->only(["id", "class_id", "name", "money", "img", "", "status", "name_en", "is_thumbs_up"]);

        $menuModel = new Menu();
        $menuModel->vaild_menu($data);


        $menu = Menu::where("id", $data["id"])->first();
        if ($menu == null) {
            throw new StoreResourceFailedException("不存在该菜品哦~");
        }
        $menu->class_id     = $data['class_id'];
        $menu->name         = $data['name'];
        $menu->money        = $data['money'] * 100;
        $menu->img          = $data['img'];
        $menu->is_thumbs_up = $data['is_thumbs_up'];
        $menu->status       = $data['status'];
        $menu->name_en      = $data['name_en'];
        $menu->save();

        return $this->response->array(['status_code' => 200, 'message' => '菜品编辑成功~']);


    }

    public function addMenu(Request $request)
    {
        $data = $request->only(["class_id", "name", "money", "img", "is_thumbs_up", "status", "name_en","sort"]);

        $menuModel = new Menu();
        $menuModel->vaild_menu($data);
        $data['money'] = $data['money'] * 100;
        $menu_id       = DB::table('menus')->insertGetId($data);

        if ($menu_id) {

            return $this->response->array(['status_code' => 200, 'message' => '菜品添加成功~']);
        } else {
            throw new StoreResourceFailedException('注册失败', ['mobile' => ['数据写入出错']]);
        }

    }

    public function sortMenu(Request $request)
    {
        $data = $request->only(["menu"]);
        foreach ($data['menu'] as $v) {
            $menu       = Menu::where("id", $v['id'])->first();
            $menu->sort = $v['sort'];
            $menu->save();
        }
        return $this->response->array(['status_code' => 200, 'message' => '成功~']);
    }

    public function sortClass(Request $request)
    {
        $data = $request->only(["class"]);
        foreach ($data['class'] as $v) {
            $class                = MenusClass::where("id", $v['id'])->first();
            $class->sort          = $v['sort'];
            $class->class_name    = $v['class_name'];
            $class->class_name_en = $v['class_name_en'];
            $class->save();
        }
        return $this->response->array(['status_code' => 200, 'message' => '成功~']);
    }

    public function addClass(Request $request)
    {
        $data                 = $request->only(["class_name", "class_name_en", "sort"]);
        $class                = new MenusClass();
        $class->class_name    = $data['class_name'];
        $class->sort          = $data['sort'];
        $class->class_name_en = $data['class_name_en'];

        if ($class->save()) {
            return $this->response->array(['status_code' => 200, 'message' => '成功~']);
        }
        return $this->response->array(['status_code' => 422, 'message' => '失败~']);
    }

    public function delClass(Request $request)
    {
        $data  = $request->only(["id"]);
        $class = MenusClass::where("id", $data['id'])->first();
        if ($class->forceDelete()) {
            return $this->response->array(['status_code' => 200, 'message' => '成功~']);
        }
        return $this->response->array(['status_code' => 422, 'message' => '失败~']);
    }
}
