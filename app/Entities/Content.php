<?php

namespace App\Entities;

use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    //
    protected $table = 'content';
    public function vaild_content($data)
    {

        if (!isset($data['title'])) {
            throw new StoreResourceFailedException('标题不能为空');
        }

        if (!isset($data['lang'])) {
            throw new StoreResourceFailedException('类型不能为空');
        }


        if (!isset($data['img'])) {
            throw new StoreResourceFailedException('图片不能为空');
        }
        if (!isset($data['content'])) {
            throw new StoreResourceFailedException('内容不能为空');
        }
        if (!isset($data['status'])) {
            throw new StoreResourceFailedException('显示不能为空');
        }

    }
}
