<?php

use Illuminate\Http\Request;

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

/**
 * Device Distro API
 */
Route::post('login', 'AuthController@login');
Route::get('list-devices', 'WarehouseController@devices');
Route::get('devices/{identifier}', 'WarehouseController@getDevice');
Route::get('check-device/{imei}', 'WarehouseController@checkIMEI');


/**
 * Tools
 */

Route::get('tasks', 'TaskController@index');
Route::get('tasks/{id}', 'TaskController@download');

Route::get('compare-sample', 'ApiController@compareSample');
Route::post('compare-sheets', 'ApiController@compareSheets');
Route::post('merge-special', 'ApiController@mergeSheets');
Route::post('bvn-validator', 'ApiController@processUpload');
