<?php


$api = app('Dingo\Api\Routing\Router');
$api->group(['prefix' => 'user', 'namespace' => 'App\Http\Controllers\User'], function ($api) {

    $api->get('test', 'TestController@test');
    $api->get('getMenu', "MenuController@getMenu");
    $api->post('getCartInfo', "CartController@getCartInfo");
    $api->post('addOrder', "CartController@addOrder");
    $api->get('list', 'ContentController@list');
    $api->get('info', 'ContentController@info');


});