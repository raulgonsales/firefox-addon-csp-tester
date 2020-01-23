<?php

Route::get('/', 'HomepageController@show');

Route::get('/test/{pageName}', 'TestController@show');