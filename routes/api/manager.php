<?php
/**
 * Created by PhpStorm.
 * User: poxiao
 * Date: 2020/4/2
 * Time: 16:56
 */


$api = app('Dingo\Api\Routing\Router');
$api->group(['prefix' => 'manager', 'namespace' => 'App\Http\Controllers\Manager'], function ($api) {

    $api->get('test', 'UserController@test');
    $api->group(['prefix' => 'user'], function ($api) {
        $api->post('login', 'UserController@login');

    });

    $api->group(['middleware' => ['manager']], function ($api) {
        $api->group(['prefix' => 'order'], function ($api) {
            $api->get('list', 'OrderController@list');
        });

        $api->group(['prefix' => 'menu'], function ($api) {
            //外卖的菜品可添加修改
            $api->post('editMenu', "MenuController@editMenu");
            $api->post('addMenu', "MenuController@addMenu");
            $api->get('getMenu', "MenuController@getMenu");
            $api->post('sortClass', "MenuController@sortClass");
            $api->post('addClass', "MenuController@addClass");
            $api->post('delClass', "MenuController@delClass");
            $api->post('sortMenu', "MenuController@sortMenu");


        });
        $api->group(['prefix' => 'content'], function ($api) {
            //外卖的菜品可添加修改
            $api->post('addContent', "ContentController@addContent");
            $api->get('list', "ContentController@list");
        });
    });


});