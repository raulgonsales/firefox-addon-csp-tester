<?php

namespace App\Http\Controllers;

use App\Addon;
use App\Site;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\Input;

class HomepageController extends Controller
{
    const FIREFOX_ADDONS_LINK = 'https://addons.mozilla.org';

    const PAGINATE_ITEM_PER_PAGE = 200;

    public function show(Request $request)
    {
        $data['firefoxLink'] = self::FIREFOX_ADDONS_LINK;

        if (isset($request->site_id)) {
            $site = Site::where('id', $request->site_id)->get()->first();

            $paginateAddons = $site->relatedAddonsWithScriptSigns()->paginate(self::PAGINATE_ITEM_PER_PAGE);
        } else {
            $paginateAddons = Addon::paginate(self::PAGINATE_ITEM_PER_PAGE);
        }

        $paginateAddons->load('cspReports');
        $data['addons'] = $paginateAddons->appends(request()->input());

        $data['sites'] = Site::all();

        return response(view('welcome')->with($data));
    }
}
