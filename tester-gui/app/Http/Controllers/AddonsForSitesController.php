<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Collections\CollectionFactory;
use Throwable;

class AddonsForSitesController extends Controller
{
    public function insert(Request $request)
    {
        if (!$request->isMethod('post')) {
            throw new \BadMethodCallException();
        }

        $addonId = $request->addon_id;

        try {
            $sitesInfoCollection = CollectionFactory::createAddonSiteInfoCollection($request->data);
        } catch (Throwable $e) {
            $sitesInfoCollection = null;

            throw new Exception("AddonId: " . $addonId . '.' . $e->getMessage());
        }


        if ($sitesInfoCollection === null || $sitesInfoCollection->count() === 0) {
            return false;
        }



        return 'kek';
    }
}
