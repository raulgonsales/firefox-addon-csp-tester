<?php

Route::get('/', 'HomepageController@show');

Route::get('/test', 'TestController@show');

Route::get('/sites-addons-report', 'ReportController@getReportForAddonSiteStat');
Route::get('/csp-errors-report', 'ReportController@getReportForCspErrorsStat');
