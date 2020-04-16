<?php

namespace App\Http\Controllers\Manager;


use App\Entities\Content;
use App\Entities\Menu;
use App\Entities\MenusClass;
use App\Http\Controllers\User\BaseController;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentController extends BaseController
{

    public function addContent(Request $request)
    {
        $data         = $request->only(["id", "title", "lang", "img", "content", "status", "author", "created_at"]);
        $contentModel = new Content();
        $contentModel->vaild_content($data);
        if (isset($data['id'])) {
            $content = Content::where("id", $data["id"])->first();
            if ($content == null) {
                throw new StoreResourceFailedException("不存在该文章哦~");
            }
            $content->title      = $data['title'];
            $content->lang       = $data['lang'];
            $content->content    = $data['content'];
            $content->img        = $data['img'];
            $content->status     = $data['status'];
            $content->author     = $data['author'];
            $content->created_at = $data['created_at'];
            $content->save();
            $content_id = $content->id;
        } else {
            $content_id = Content::insertGetId($data);
        }
        if ($content_id) {
            return $this->response->array(['status_code' => 200, 'message' => '文章添加成功~']);
        } else {
            throw new StoreResourceFailedException('注册失败', ['mobile' => ['数据写入出错']]);
        }

    }

    public function list(Request $request)
    {
        $data = $request->only('pageSize');
        $list = DB::table('content')
            ->orderByDesc("id")
            ->paginate($data['pageSize']);
        return $this->returnArray(200, '', $list);
    }

}
