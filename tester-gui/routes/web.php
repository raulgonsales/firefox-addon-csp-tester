<?php

Route::get('/', 'HomepageController@show');

Route::get('/test/{pageName}', 'TestController@show');

Route::post('/store-links', 'PostLinksController@store')->name('test');