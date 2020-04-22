<?php

namespace App\Http\Controllers;

use App\Addon;
use App\Site;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    const FIREFOX_ADDONS_LINK = 'https://addons.mozilla.org';

    const POSSIBLE_ERROR_TYPES = [
        'on-start-test',
        'no-errors'
    ];

    const PAGINATE_ITEM_PER_PAGE = 500;

    public function show(Request $request)
    {
        $data['firefoxLink'] = self::FIREFOX_ADDONS_LINK;

        if (isset($request->errorType) && in_array($request->errorType, self::POSSIBLE_ERROR_TYPES)) {
            if ($request->errorType === 'no-errors') {
                $data['addons'] = Addon::where('csp_error_type', null)->get();
            } else {
                $data['addons'] = Addon::where('csp_error_type', $request->errorType)->get();
            }
        } else {
            $paginateAddons = Addon::paginate(self::PAGINATE_ITEM_PER_PAGE);
            $paginateAddons->load('cspReports');
            $data['addons'] = $paginateAddons;
        }

        $data['sites'] = Site::all();

        return response(view('welcome')->with($data));
    }
}
