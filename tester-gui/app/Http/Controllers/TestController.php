<?php

namespace App\Http\Controllers;

class TestController extends Controller
{
	public function show($pageName) {
		return response(view('testing-pages.' . $pageName), 200)
			->header("Content-Security-Policy", "script-src 'none'; report-uri https://webhook.site/29b33fda-fec3-418f-abfb-5d89252f3996");
	}
}