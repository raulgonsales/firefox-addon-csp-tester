<?php

Route::get('/', 'HomepageController@show');

Route::get('/test', 'TestController@show');

Route::get('/sites-addons-report', 'AddonsForSitesController@showAll');
