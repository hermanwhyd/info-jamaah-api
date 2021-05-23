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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'api'], function ($router) {
    $router->get('unauthorize', 'AuthController@unauthorize')->name('unauthorize');
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
    $router->post('refresh-token', 'AuthController@refresh');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'auth'], function ($router) {
    $router->post('logout', 'AuthController@logout');
    $router->get('me', 'AuthController@me');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'v1'], function ($router) {

    // user
    $router->get('user', 'UserController@findAll');
    $router->get('user/paging', 'UserController@paging');
    $router->get('user/{id}', 'UserController@find');
    $router->post('user', 'UserController@store');
    $router->put('user/{id}', 'UserController@update');
    $router->delete('user/{id}', 'UserController@destroy');

    // shared property
    $router->post('shared-props', 'SharedPropertyController@store');
    $router->put('shared-props/{id}', 'SharedPropertyController@update');
    $router->delete('shared-props/{id}', 'SharedPropertyController@destroy');
    $router->put('shared-props/batch-update', 'SharedPropertyController@batchUpdate');
    $router->get('shared-props/group/{group}', 'SharedPropertyController@getByGroup');
    $router->get('shared-props/options/{selector}', 'SharedPropertyController@getOptionBySelector');

    // custom-field
    $router->get('custom-field/{id}', 'CustomFieldController@find');
    $router->post('custom-field', 'CustomFieldController@store');
    $router->put('custom-field/{id}', 'CustomFieldController@update');
    $router->delete('custom-field/{id}', 'CustomFieldController@destroy');
    $router->put('custom-field/batch-update', 'CustomFieldController@batchUpdate');

    // additional-field
    $router->get('additional-field/{id}', 'AdditionalFieldController@find');
    $router->put('additional-field/{id}', 'AdditionalFieldController@update');
    $router->delete('additional-field/{id}', 'AdditionalFieldController@destroy');

    // jamaah
    $router->get('jamaah/paging', 'JamaahController@paging');
    $router->get('jamaah', 'JamaahController@getAll');
    $router->get('jamaah/{id}', 'JamaahController@findById');
    $router->post('jamaah', 'JamaahController@store');
    $router->post('jamaah/{id}/photo', 'JamaahController@updatePhoto');

    // asset sb
    $router->group(['prefix' => 'asset'], function () use ($router) {
        $router->get('', 'AssetController@getAll');
        $router->get('{id}', 'AssetController@findById');
        $router->get('{id}/detail', 'AssetController@findAddFieldsById');
        $router->post('', 'AssetController@store');
        $router->put('{id}', 'AssetController@update');
        $router->post('{id}/upload', 'AssetController@upload');

        // additional field
        $router->post('{id}/detail', 'AssetController@setAdditionalField');

        // maintenance
        $router->group(['prefix' => 'maintenance'], function () use ($router) {
            $router->get('', 'AssetMaintenanceController@getAll');
            $router->get('paging', 'AssetMaintenanceController@paging');
            $router->get('{id}', 'AssetMaintenanceController@findById');
            $router->post('', 'AssetMaintenanceController@store');
            $router->put('{id}', 'AssetMaintenanceController@update');
            $router->delete('{id}', 'AssetMaintenanceController@destroy');
        });

        // audit
        $router->group(['prefix' => 'audit'], function () use ($router) {
            $router->get('', 'AssetAuditController@getAll');
            $router->get('paging', 'AssetAuditController@paging');
            $router->get('{id}', 'AssetAuditController@findById');
            $router->post('', 'AssetAuditController@store');
            $router->put('{id}', 'AssetAuditController@update');
            $router->delete('{id}', 'AssetAuditController@destroy');
        });
    });

    $router->group(['prefix' => 'media'], function () use ($router) {
        $router->get('/{uuid}/download', 'MediaController@downloadSingle')->name('media.download');
        $router->put('/{uuid}', 'MediaController@update');
        $router->delete('/{uuid}', 'MediaController@destroy');
    });
});
