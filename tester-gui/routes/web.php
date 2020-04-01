<?php

Route::get('/', 'HomepageController@show');

Route::get('/test/{pageName}', 'TestController@show');

Route::get('/sites-addons-report', 'AddonsForSitesController@showAll');
