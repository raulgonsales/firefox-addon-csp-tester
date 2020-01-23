<?php

namespace App\Http\Controllers;

use App\CspReport;
use Illuminate\Http\Request;

class PostCspReportsController extends Controller
{
	public function store(Request $request)
	{
		$cspReportModel = new CspReport;

		$incomingCspReport = $request->toArray()['csp-report'];

		$cspReportModel->document_uri = $incomingCspReport['document-uri'];
		$cspReportModel->original_policy = $incomingCspReport['original-policy'];
		$cspReportModel->source_file = $incomingCspReport['source-file'];
		$cspReportModel->violated_directive = $incomingCspReport['violated-directive'];

		$cspReportModel->save();
	}
}
