<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['prefix' => 'api', 'middleware' => ['cors']], function ($api) {

    Route::prefix('')
        ->group(base_path('routes/api/user.php'));

    //管理员端路由组
    Route::prefix('')
        ->group(base_path('routes/api/manager.php'));

});