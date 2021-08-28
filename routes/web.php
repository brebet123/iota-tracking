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

$router->get('refresh-token', 'Auth\AuthController@refreshToken');

$router->post('login', 'Auth\AuthController@login');
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->post('register', 'Auth\AuthController@register');
    $router->get('/activity/get-list', 'ActivityController@getList');
    $router->get('/activity/get-list-member', 'ActivityController@getListMember');
    $router->get('/activity/get-list-members', 'ActivityController@getListMembers');
    $router->post('/activity/add', 'ActivityController@add');
    $router->get('get-leader-board', 'ActivityController@getLeaderBoard');
    $router->get('get-view-shop-history', 'ActivityController@getViewHistory');
});

$router->group(['middleware' => 'authClient'], function () use ($router) {
    $router->get('client/activity/get-list', 'ActivityController@getListDataUpdated');
    $router->get('client/activity/get-list-member', 'ActivityController@getListMember');
});

$router->get('/polyline/tes', 'ActivityController@tes');