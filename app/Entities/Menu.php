<?php

namespace App\Entities;

use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    //
    protected $table = 'menus';

    public function vaild_menu($data)
    {
//        if (empty($data['id'])) {
//            throw new StoreResourceFailedException('id 不能为空');
//        }

        if (empty($data['class_id'])) {
            throw new StoreResourceFailedException('类目不能为空');
        }

        if (!isset($data['name'])) {
            throw new StoreResourceFailedException('名称不能为空');
        }

        if (!isset($data['money'])) {
            throw new StoreResourceFailedException('金额不能为空');
        }

        if (!isset($data['img'])) {
            throw new StoreResourceFailedException('图片不能为空');
        }
        if (!isset($data['is_thumbs_up'])) {
            throw new StoreResourceFailedException('推荐不能为空');
        }
        if (!isset($data['status'])) {
            throw new StoreResourceFailedException('显示不能为空');
        }

        if (!isset($data['name_en'])) {
            throw new StoreResourceFailedException('英文名称不能为空');
        }
    }
}
