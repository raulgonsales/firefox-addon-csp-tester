<?php

namespace App\Http\Controllers;

use App\Addon;
use App\CspReport;
use Illuminate\Http\Request;

class PostCspReportsController extends Controller
{
    public function store(Request $request, $testType, $addonId)
    {
        if (!$request->isMethod('post')) {
            throw new \BadMethodCallException();
        }

        $incomingCspReport = $request->toArray()['csp-report'];

        $cspReportModel = new CspReport;
        $cspReportModel->document_uri = $incomingCspReport['document-uri'];
        $cspReportModel->original_policy = $incomingCspReport['original-policy'];
        $cspReportModel->source_file = $incomingCspReport['source-file'];
        $cspReportModel->violated_directive = $incomingCspReport['violated-directive'];
        $cspReportModel->addon_id = $addonId;
        $cspReportModel->test_type = $testType;

        $addon = Addon::find($addonId);
        $addon->cspReports()->save($cspReportModel);
    }
}
