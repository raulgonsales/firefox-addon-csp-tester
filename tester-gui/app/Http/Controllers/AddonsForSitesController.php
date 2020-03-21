<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddonsForSitesController extends Controller
{
    public function insert(Request $request)
    {
        if (!$request->isMethod('post')) {
            throw new \BadMethodCallException();
        }


    }
}
