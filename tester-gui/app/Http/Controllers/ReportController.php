<?php

namespace App\Http\Controllers;

use App\Addon;
use Illuminate\Http\Request;
use Psy\Util\Json;

class ReportController extends Controller
{
    public function getForAll()
    {
        $allAddons = Addon::all();

        $noError = $allAddons->where('csp_error_type', null);
        $initialError = $allAddons->where('csp_error_type', 'initial-error');
        $response = [
            'count' => $allAddons->count(),
            'by_errors' => [
                'no_error' => [
                    'count' => $noError->count(),
                    'items' => $noError
                ],
                'initial_error' => [
                    'count' => $initialError->count(),
                    'items' => $initialError
                ]
            ]
        ];

        return Json::encode($response);
    }

    public function render(Request $request)
    {
        if (!isset($request->data)) {
            throw new \Exception('Data for rendering report not provided!');
        }

        $data = $request->data;

        $view = view('reportModal');
        $view->test = 'kek';
        echo $view->render();
    }
}
