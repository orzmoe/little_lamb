<?php

namespace App\Http\Controllers\User;


use App\Entities\Menu;
use App\Entities\MenusClass;
use App\Entities\Order;
use App\Entities\OrderInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends BaseController
{
    //
    public function getCartInfo(Request $request)
    {
        //
        $requestData  = $request->only('ids', 'num', 'lang');
        $menu         = DB::table('menus')->leftJoin('menus_class', 'menus_class.id', '=', 'menus.class_id')
            ->select("menus.*", "menus_class.class_name", "menus_class.class_name_en")
            ->whereIn("menus.id", $requestData["ids"])
            ->get();
        $data["list"] = $menu;
        $data["num"]  = $requestData["num"];
        $money        = 0;
        foreach ($menu as $k => $v) {
            //$money           += $v->money * $this->id2num($v->id, $requestData);
            $money           = bcadd($money, bcmul($v->money, $this->id2num($v->id, $requestData), 2), 2);
            $data['num'][$k] = $this->id2num($v->id, $requestData);
        }

        $data['tax']  = bcmul($money, env("TAX"), 0);
        $data['ship'] = "$0";
        if ($money < 10000) {
            if ($requestData['lang'] == 'en') {
                $data['ship'] = "$1/ km(activity exemption)";
            } else {
                $data['ship'] = "每公里1新币(活动减免)";
            }
        }
        $data["money"] = $money;
        $data["total"] = round(bcadd($money, $data['tax'], 2) / 100, 2) * 100;


        return $this->returnArray(200, '', $data);
    }

    public function addOrder(Request $request)
    {
        $requestData = $request->only('phone', 'name', 'address', 'people', 'remark', 'cart', 'time', 'pay_type');
        $menu        = DB::table('menus')->leftJoin('menus_class', 'menus_class.id', '=', 'menus.class_id')
            ->select("menus.*", "menus_class.class_name", "menus_class.class_name_en")
            ->whereIn("menus.id", $requestData["cart"]["ids"])
            ->get();
        $money       = 0;
        $context     = "";
        foreach ($menu as $k => $v) {
            /*$menu[$k]->num = $this->id2num($v->id, $requestData['cart']);
            $money         += $v->money * $menu[$k]->num;*/
            $menu[$k]->num = $this->id2num($v->id, $requestData['cart']);
            /* $money         += bcmul($v->money, $menu[$k]->num);*/
            $money   = bcadd($money, bcmul($v->money, $menu[$k]->num, 2), 2);
            $context .= $v->name . " X " . $menu[$k]->num . "\n";
        }

        $tax  = bcmul($money, env("TAX"), 0);
        $ship = 0;
        if ($money < 10000) {
            $ship = env("SHIP");
        }
        $money = bcadd($money, $tax, 2);
        $money = round(bcadd($money, $tax, 2) / 100, 2) * 100;
        $order           = new Order();
        $order->phone    = $requestData['phone'];
        $order->name     = $requestData['name'];
        $order->address  = $requestData['address'];
        $order->people   = $requestData['people'];
        $order->remark   = $requestData['remark'];
        $order->time     = $requestData['time'];
        $order->tax      = $tax;
        $order->ship     = $ship;
        $order->money    = $money;
        $order->pay_type = $requestData['pay_type'];
        $order->save();
        foreach ($menu as $k => $v) {
            $info        = new OrderInfo();
            $info->mid   = $v->id;
            $info->num   = $v->num;
            $info->money = $v->money;
            $info->oid   = $order->id;
            $info->save();
        }
        //http://manage.littlelamb.sg/web/newOrder
        $data['money']  = $money / 100;
        $data['remark'] = $order->remark;
        //$data['payType'] = $this->getPayType($order->pay_type);
        $data['info']    = "\n手机号：$order->phone\n姓名：$order->name\n地址：$order->address\n人数：$order->people\n支付方式:" . $this->getPayType($order->pay_type);
        $data['context'] = rtrim($context, "\n");
        $this->curlGet("http://manage.littlelamb.sg/web/newOrder", $data);
        return $this->returnArray(200, '提交成功', []);
    }

    public function id2num($ids, $list)
    {
        foreach ($list['ids'] as $k => $id) {
            foreach ($list['num'] as $v) {
                if ($v['id'] == $ids) {
                    return $v['num'];
                }
            }
        }
        return 0;
    }

    public function curlGet($url = '', $options = [])
    {

        $aHeader = array('user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.70 Safari/537.36');
        $ch      = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($options)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($options));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function getPayType($name)
    {
        switch ($name) {
            case "paynow":
                return "paynow";
            case "cash_on_delivery":
                return "货到付款";
        }
        return "货到付款";
    }
}
