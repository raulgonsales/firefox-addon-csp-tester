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

Route::post('store-links', 'PostLinksController@store')->name('test');
Route::post('store-csp-reports/{test_type}/{addon_id}', 'PostCspReportsController@store');
Route::post('save-content-scripts-info', 'AddonsForSitesController@insert');

Route::get('report-for-all', 'ReportController@getForAll');
Route::post('render-report', 'ReportController@render');
