<?php

namespace App\Http\Controllers\User;


use Illuminate\Http\Request;

class TestController extends BaseController
{
    //
    public function test(Request $request)
    {
        //
        return $this->response->array(['status_code' => 200, 'message' => '0.0']);
    }
}
