<?php

namespace App\Http\Controllers;

use App\Addon;
use App\CspReport;
use Illuminate\Http\Request;

class UpdateAddonCspStatusController extends Controller
{
	const CSP_REPORT_DELAY_SEC = 5;

    public function update(Request $request)
	{
		if (!$request->isMethod('post')) {
			throw new \BadMethodCallException();
		}

		sleep(self::CSP_REPORT_DELAY_SEC);

		if ($cspReportsCount = ($cspReports = CspReport::all())->count()) {
			$addonRecord = Addon::where('id', $request->addon_id)->first();
			$addonRecord->csp_error_type = $request->csp_error_type;
			$addonRecord->csp_reports_count = $cspReports->count();
			$addonRecord->save();

			/** @var CspReport $report */
			foreach ($cspReports as $report) {
				$report->delete();
			}
		}

		return $cspReportsCount > 0 ? 'Error found (' . $request->csp_error_type . '): CSP status updated' : 'No errors found';
	}
}
