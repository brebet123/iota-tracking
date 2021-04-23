<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->post('login', 'Auth\AuthController@login');
    $router->post('register', 'Auth\AuthController@register');
    $router->get('/activity/get-list', 'ActivityController@getList');
    $router->get('/activity/get-list-member', 'ActivityController@getListMember');
    $router->post('/activity/add', 'ActivityController@add');
});

$router->group(['middleware' => 'authClient'], function () use ($router) {
    $router->get('client/activity/get-list', 'ActivityController@getList');
    $router->get('client/activity/get-list-member', 'ActivityController@getListMember');
});