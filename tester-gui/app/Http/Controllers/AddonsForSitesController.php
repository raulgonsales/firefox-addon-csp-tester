<?php

namespace App\Http\Controllers;

use App\Addon;
use App\Models\Collections\AddonSiteItems\SiteInfo;
use Exception;
use Illuminate\Http\Request;
use App\Models\Collections\CollectionFactory;
use Throwable;

class AddonsForSitesController extends Controller
{
    public function insert(Request $request): void
    {
        if (!$request->isMethod('post')) {
            throw new \BadMethodCallException();
        }

        if (!isset($request->addon_id) || !isset($request->data)) {
            throw new Exception('Bad data provided to save addon site info');
        }

        $addon = Addon::find($request->addon_id);

        try {
            $sitesInfoCollection = CollectionFactory::createAddonSiteInfoCollection($request->data);
        } catch (Throwable $e) {
            $sitesInfoCollection = null;

            throw new Exception("AddonId: " . $request->addon_id . '.' . $e->getMessage());
        }


        if ($sitesInfoCollection === null || $sitesInfoCollection->count() === 0) {
            return;
        }

        /** @var SiteInfo $siteInfo */
        foreach ($sitesInfoCollection as $siteInfo) {
            $addon->sites()->sync([$siteInfo->getSiteId(), [
                'content_scripts_count' => $siteInfo->getContentScriptsCount(),
                'content_scripts_count_with_signs' => $siteInfo->getContentScriptsCountWithSigns()
            ]]);
        }
    }
}
