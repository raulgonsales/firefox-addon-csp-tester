<?php

namespace App\Http\Controllers;

class TestController extends Controller
{
	public function show($pageName) {
		return response(view('testing-pages.' . $pageName), 200)
			->header("Content-Security-Policy", "script-src 'none'; report-uri http://172.19.0.6/api/store-csp-reports");
	}
}
