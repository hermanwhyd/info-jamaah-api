<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->group(['prefix' => 'api'], function ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
    $router->post('refresh-token', 'AuthController@refresh');
});

$router->group(['middleware' => 'auth:api', 'prefix' => 'auth'], function ($router) {
    $router->post('logout', 'AuthController@logout');
    $router->get('me', 'AuthController@me');
});
// $router->group(['prefix' => 'v1'], function ($router) {
$router->group(['middleware' => 'auth:api', 'prefix' => 'v1'], function ($router) {

    // user
    $router->get('user', 'UserController@findAll');
    $router->get('user/paging', 'UserController@paging');
    $router->get('user/{id:[0-9]+}', 'UserController@find');
    $router->post('user', 'UserController@store');
    $router->put('user/{id:[0-9]+}', 'UserController@update');
    $router->delete('user/{id:[0-9]+}', 'UserController@destroy');

    // shared property
    $router->post('shared-props', 'SharedPropertyController@store');
    $router->put('shared-props/{id:[0-9]+}', 'SharedPropertyController@update');
    $router->delete('shared-props/{id:[0-9]+}', 'SharedPropertyController@destroy');
    $router->put('shared-props/batch-update', 'SharedPropertyController@batchUpdate');
    $router->get('shared-props/group/{group}', 'SharedPropertyController@getByGroup');
    $router->get('shared-props/options/{model}', 'SharedPropertyController@getOptionByModel');

    // tag
    $router->post('tag', 'TagController@store');
    $router->put('tag/{id:[0-9]+}', 'TagController@update');
    $router->delete('tag/{id:[0-9]+}', 'TagController@destroy');
    $router->get('tag/group/{group}', 'TagController@getByGroup');

});
