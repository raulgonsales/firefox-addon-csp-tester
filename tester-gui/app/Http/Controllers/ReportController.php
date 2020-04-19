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

    public function getReportForAddonSiteStat()
    {
        $sites = Site::all()->load('relatedAddons');

        return response(
            view('sitesAddonsReport')->with([
                'sites' => $sites
            ])
        );
    }
}
