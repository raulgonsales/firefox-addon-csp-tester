<?php

namespace App\Http\Controllers;

use App\Addon;
use Exception;
use Illuminate\Http\Request;

class PostLinksController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->isMethod('post')) {
            throw new \BadMethodCallException();
        }

        $addonsInfoBatch = $request->all();

        foreach ($addonsInfoBatch as $item) {
            try {
                Addon::insert($item);
            } catch(Exception $exception) {
                continue;
            }
        }
    }
}
