<?php

namespace App\Http\Controllers;

use App\Addon;
use App\Models\Collections\AddonSiteItems\SiteInfo;
use App\Site;
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
            return false;
        }

        /** @var SiteInfo $siteInfo */
        foreach ($sitesInfoCollection as $siteInfo) {
            $addon->sites()->sync([
                $siteInfo->getSiteId() => [
                    'content_scripts_count' => $siteInfo->getContentScriptsCount(),
                    'content_scripts_count_with_signs' => $siteInfo->getContentScriptsCountWithSigns(),
                    'scripts_info' => $siteInfo->getScriptsInfoCollection() !== null ?
                        json_encode($request->data[$siteInfo->getSiteId()]['scripts_info'])
                        : null
                ]
            ], false);
        }

        return response()->json(['success' => 'success'], 200);
    }

    public function showAll(Request $request)
    {
        $sites = Site::all();

        foreach ($sites as $site) {
            $site->load('relatedAddons');
        }

        return response(
            view('sitesAddonsReport')->with([
                'sites' => $sites
            ])
        );
    }
}
