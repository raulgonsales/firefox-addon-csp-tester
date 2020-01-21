<?php

namespace App\Http\Controllers;

use App\Addon;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
	const FIREFOX_ADDONS_LINK = 'https://addons.mozilla.org';

    public function show()
	{
		$data['addons'] = Addon::all();
		$data['firefoxLink'] = self::FIREFOX_ADDONS_LINK;

		return response(view('welcome')->with($data));
	}
}
