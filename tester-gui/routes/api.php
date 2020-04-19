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

Route::post('store-csp-reports/{test_type}/{addon_id}', 'PostCspReportsController@store');
Route::post('save-content-scripts-info', 'AddonsForSitesController@insert');

Route::prefix('backend-call')->group(function () {
    Route::post('test/{test_type}', 'AjaxController@startTestBackendCall');
    Route::post('content-scripts-analysis', 'AjaxController@startContentScriptAnalysis');
});

Route::post('csp-error-stat-per-addon', 'ReportController@getCspStatisticPerAddon');
