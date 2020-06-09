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
            $v->desc = mb_substr(strip_tags($v->content),0,90);
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

    public function CloseTags($html)
    {
        // 直接过滤错误的标签 <[^>]的含义是 匹配只有<而没有>的标签
        // 而preg_replace会把匹配到的用''进行替换
        $html = preg_replace('/<[^>]*$/', '', $html);

        // 匹配开始标签，这里添加了1-6，是为了匹配h1~h6标签
        preg_match_all('#<([a-z1-6]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $opentags = $result[1];
        // 匹配结束标签
        preg_match_all('#</([a-z1-6]+)>#iU', $html, $result);
        $closetags = $result[1];
        $len_opened = count($opentags);
        // 如何两种标签数目一致 说明截取正好
        if (count($closetags) == $len_opened) {
            return $html;
        }

        $opentags = array_reverse($opentags);
        // 过滤自闭和标签，也可以在正则中过滤 <(?!meta|img|br|hr|input)>
        $sc = array('br', 'input', 'img', 'hr', 'meta', 'link');

        for ($i = 0; $i < $len_opened; $i++) {
            $ot = strtolower($opentags[$i]);
            if (!in_array($opentags[$i], $closetags) && !in_array($ot, $sc)) {
                $html .= '</' . $opentags[$i] . '>';
            } else {
                unset($closetags[array_search($opentags[$i], $closetags)]);
            }
        }
        return $html;
    }
}
