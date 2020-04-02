<?php

namespace App\Http\Controllers;

use App\Addon;
use Illuminate\Http\Request;

class PostLinksController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->isMethod('post')) {
            throw new \BadMethodCallException();
        }

        $addonsInfoBatch = $request->all();

        $batch = [];
        foreach ($addonsInfoBatch as $addon) {
            $batch[] = [
                'name' => $addon['name'],
                'link' => $addon['link'],
                'file_name' => $addon['file_name'],
                'img_name' => $addon['img_name'],
                'users_count' => 1,
                'firefox_recommend' => true
            ];
        }

        Addon::insert($batch);
    }
}
