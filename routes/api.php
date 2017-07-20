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
Route::post('devices', 'WarehouseController@registerDevice');

Route::get('devices/{identifier}', 'WarehouseController@getDevice');
Route::get('check-device/{imei}', 'WarehouseController@checkIMEI');

Route::get('unbundle/{uuid}', 'WarehouseController@unbundle');
Route::get('unbundling/{uuid}/{category}/{value}', 'WarehouseController@unbundling');

Route::post('enroll', 'WarehouseController@enroll');
Route::get('states', 'WarehouseController@getStates');
Route::get('states/{id}', 'WarehouseController@getLgas');

Route::get('volunteer/{device_id}/{state_id}/{lga_id}', 'WarehouseController@nextVolunteer');

Route::post('allocate', 'WarehouseController@allocateDevice');
Route::get('centers/{id}', 'WarehouseController@pickupLocations');
Route::get('dispatch/{id}/{collectionCenter}', 'WarehouseController@dispatchDevice');


/**
 * Tools
 */

Route::get('tasks', 'TaskController@index');
Route::get('tasks/{id}', 'TaskController@download');

Route::get('compare-sample', 'ApiController@compareSample');
Route::post('compare-sheets', 'ApiController@compareSheets');
Route::post('merge-special', 'ApiController@mergeSheets');
Route::post('bvn-validator', 'ApiController@processUpload');
