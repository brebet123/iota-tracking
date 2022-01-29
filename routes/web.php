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

$router->get('/php-info', function () use ($router) {
    // return $router->app->version();
    return phpinfo();
});

$router->get('refresh-token', 'Auth\AuthController@refreshToken');
$router->get('version', 'Auth\AuthController@version');

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
    $router->get('client/race/get-list', 'ActivityController@getListRace');
    $router->get('client/race/leaderboard', 'ActivityController@leaderBoard');
});

$router->get('/polyline/tes', 'ActivityController@tes');
$router->get('/getact', 'ActivityController@updateInsertDataRestep');

$router->group(['namespace' => '\Rap2hpoutre\LaravelLogViewer'], function() use ($router) {
    $router->get('logs', 'LogViewerController@index');
});