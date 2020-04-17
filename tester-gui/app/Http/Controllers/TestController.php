<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function show(Request $request) {
        if (!isset($request->test_type)) {
            return response('Please, specify test_type', 400);
        }
        if (!isset($request->addon_id)) {
            return response('Please, specify addon_id', 400);
        }

        return response(view('testing-pages.page'), 200)->header(
            "Content-Security-Policy",
            "script-src 'none'; report-uri http://172.22.0.5/api/store-csp-reports/" . $request->test_type . '/' . $request->addon_id
        );
    }
}
