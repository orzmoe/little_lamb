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

class UserController extends BaseController
{


    public function test()
    {
        echo "222";
    }

    public function login(Request $request)
    {
        $data = $request->only('username', 'password');
        $info = Admin::where(['username' => $data['username']])->first();
        if (!$info) {
            return $this->returnArray('用户名不存在', '', 422);
        }
        if ($data['password'] == $info['password']) {
            // 密码匹配...
            /* session(['userInfo' => $info]);*/
            unset($info['password']);
            $info['token'] = $this->setUserToken($info);
            return $this->returnArray('200', "登陆成功", $info);
        } else {
            return $this->returnArray(422,'密码错误', []);
        }
    }
}