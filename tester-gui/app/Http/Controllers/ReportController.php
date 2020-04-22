<?php

namespace App\Http\Controllers;

use App\Addon;
use App\CspReport;
use App\Models\Enum\TestTypesEnum;
use App\Site;
use Illuminate\Http\Request;
use Psy\Util\Json;

class ReportController extends Controller
{
    public function getReportForCspErrorsStat()
    {
        $data['addonsCount'] = Addon::count();
        $data['onStartTestAddonsCount'] = Addon::select(['addons.id'])
            ->leftJoin('csp_reports', 'csp_reports.addon_id', '=', 'addons.id')
            ->where('test_type', '=', 'on-start-test')
            ->whereNotNull('csp_reports.id')->distinct()->get()->count();
        $data['noCspErrorsAddons'] = Addon::select(['addons.id'])
            ->leftJoin('csp_reports', 'csp_reports.addon_id', '=', 'addons.id')
            ->whereNull('csp_reports.id')
            ->get()
            ->count();

        $data['graphDataPoints'] = [
            ['y' => $data['noCspErrorsAddons'], 'label' => TestTypesEnum::NO_ERROR()->value],
            ['y' => $data['onStartTestAddonsCount'], 'label' => TestTypesEnum::ON_START_TESTS()->value]
        ];

        return response(
            view('cspErrorReport')->with($data)
        );
    }

    public function getCspStatisticPerAddon(string $testType)
    {
        $data['kek'] = Addon::addonsWithExistedCspReports('on-start-test');

        return response(
            view('cspErrorStatisticPerAddon')->with($data)
        );
    }

    public function getReportForAddonSiteStat(Request $request)
    {
        if (!isset($request->firefox_recommend)) {
            $firefoxRecommend = null;
        }
        $firefoxRecommend = $request->firefox_recommend;

        $data['graphDataPoints'] = [];

        foreach (Site::withCount(['relatedAddonsWithScriptSigns' => function($query) use ($firefoxRecommend) {
            if ($firefoxRecommend !== null) {
                $query->where('firefox_recommend', '=', $firefoxRecommend);
            }
        }])->get() as $site) {
            $data['graphDataPoints'][] = [
                'y' => $site->related_addons_with_script_signs_count,
                'label' => $site->site_name,
                'indexLabelFontSize' => 15,
                'site_id' => $site->id
            ];
        }

        if (isset($request->site_id)) {
            $data['siteInfo'] = Site::with(['relatedAddonsWithScriptSigns' => function($query)  use ($firefoxRecommend) {
                if ($firefoxRecommend !== null) {
                    $query->where('firefox_recommend', '=', $firefoxRecommend);
                }
            }])->where('id', '=', $request->site_id)->get()->first();
        }

        return response(
            view('sitesAddonsReport')->with($data)
        );
    }
}
