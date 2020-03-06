<?php

namespace App\Http\Controllers;

use App\Addon;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
	const FIREFOX_ADDONS_LINK = 'https://addons.mozilla.org';

	const POSSIBLE_ERROR_TYPES = [
		'initial-error'
	];

    public function show(Request $request)
	{
		$data['firefoxLink'] = self::FIREFOX_ADDONS_LINK;

		if (isset($request->errorType) && in_array($request->errorType, self::POSSIBLE_ERROR_TYPES)) {
			$data['addons'] = Addon::where('csp_error_type', $request->errorType)->get();
		} else {
			$data['addons'] = Addon::all();
		}

		return response(view('welcome')->with($data));
	}
}
