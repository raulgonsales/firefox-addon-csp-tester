<?php

namespace App\Http\Controllers;

use App\Addon;
use App\AddonTest;
use App\Models\Collections\AddonSiteItems\SiteInfo;
use App\Models\Collections\CollectionFactory;
use App\Models\Enum\TestTypesEnum;
use BadMethodCallException;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class AjaxController extends Controller
{
    /**
     * @param  Request  $request
     * @param  string  $testType
     * @return \Psr\Http\Message\StreamInterface|string|bool
     * @throws Exception
     */
    public function startTestBackendCall(Request $request, string $testType)
    {
        if (!isset($request->addon_id) || !isset($request->domain) || !isset($request->test_type)) {
            throw new InvalidArgumentException();
        }

        if (!$request->isMethod('post')) {
            throw new BadMethodCallException();
        }

        $addon = Addon::where('id', $request->addon_id)->get()->first();

        try {
            $testType = new TestTypesEnum($testType);
        } catch (InvalidEnumMemberException $exception) {
            Log::error("\e[31mFailed to test addon. Bad test type given!\e[97m", [
                'addon_id' => $addon->id,
                'test_type' => $testType
            ]);

            $addon->addonTests()->create([
                'type_name' => $request->test_type,
                'failed_test' => true
            ]);

            throw new InvalidArgumentException();
        }

        $existedNotFailedAddonTests = $addon->addonTests()
            ->where('type_name', '=', $request->test_type)
            ->whereNull('failed_test')
            ->get();
        if ($existedNotFailedAddonTests->count() > 0) {
            Log::debug('Addon already tested successfully!', [
                'addon_id' => $addon->id,
                'test_type' => $request->test_type
            ]);
            return $existedNotFailedAddonTests->toJson();
        }

        $guzzleClient = new Client();
        try {
            $response = $guzzleClient->request(
                'POST',
                env('BACKEND_API_ENDPOINT') . '/test/' . $testType->value,
                [
                    'form_params' => [
                        'id' => $request->addon_id,
                        'name' => $request->addon_name,
                        'link' => $request->addon_link,
                        'file' => $request->addon_file,
                        'test_type' => $request->test_type,
                        'domain' => $request->domain
                    ]
                ]
            );
        } catch (Exception $exception) {
            Log::error("\e[31mFailed to test addon. Bad response from backend service!\e[97m", [
                'addon_id' => $addon->id,
                'test_type' => $request->test_type,
                'exception' => $exception->getMessage()
            ]);

            $addon->addonTests()->create([
                'type_name' => $request->test_type,
                'failed_test' => true
            ]);

            return response()->json(['exception' => $exception->getMessage()], 500);
        }

        if ($response->getStatusCode() === 200) {
            $allAddonTests = $addon->addonTests()->where('type_name', '=', $request->test_type)->get();

            /** @var AddonTest $test */
            foreach ($allAddonTests as $test) {
                $test->delete();
            }
            $addon->addonTests()->create([
                'type_name' => $request->test_type
            ]);

            return $response->getBody();
        } else {
            Log::error("\e[31mFailed to test addon. Bad response from backend service!\e[97m", [
                'addon_id' => $addon->id,
                'test_type' => $request->test_type,
                'status_code' => $response->getStatusCode(),
                'body' => $response->getBody()
            ]);

            $addon->addonTests()->create([
                'type_name' => $request->test_type,
                'failed_test' => true
            ]);
        }

        return $response->getBody();
    }

    /**
     * @param  Request  $request
     * @return \Psr\Http\Message\StreamInterface|string|bool
     * @throws Exception
     */
    public function startContentScriptAnalysis(Request $request)
    {
        if (!$request->isMethod('post')) {
            Log::error("\e[31mFailed to analyze addon. Bad method call!\e[97m");
            throw new BadMethodCallException();
        }

        if (!isset($request->addon_id) || !isset($request->sites_matching)) {
            Log::error("\e[31mFailed to analyze addon. Property sites_matching or addon_id do not exist!\e[97m");
            throw new InvalidArgumentException();
        }

        $addon = Addon::find($request->addon_id);
        $sitesMatching = json_decode($request->sites_matching, true);

        foreach ($sitesMatching as $siteId => $site) {
            if ($addon->sites()->where('site_id', '=', $siteId)->get()->count() > 0) {
                unset($sitesMatching[$siteId]);
            }
        }

        if (empty($sitesMatching)) {
            Log::debug("Addon analysis for given sites were provided!", [
                'addon_id' => $addon->id,
                'sites_matching' => $request->sites_matching
            ]);

            return 'Tests are already provided!';
        }

        $guzzleClient = new Client();
        try {
            $response = $guzzleClient->request(
                'POST',
                env('BACKEND_API_ENDPOINT') . '/test/content-scripts-analysis',
                [
                    'form_params' => [
                        'id' => $request->addon_id,
                        'name' => $request->addon_name,
                        'link' => $request->addon_link,
                        'file' => $request->addon_file,
                        'sites_matching' => json_encode($sitesMatching)
                    ]
                ]
            );
        } catch (Exception $exception) {
            Log::error("\e[31mFailed to analyze addon. Bad response from backend service!\e[97m", [
                'addon_id' => $addon->id,
                'exception' => $exception->getMessage()
            ]);

            return response()->json(['failed' => 'failed'], 500);
        }

        if ($response->getStatusCode() !== 200) {
            Log::error("\e[31mFailed to analyze addon. Bad response from backend service!\e[97m", [
                'addon_id' => $addon->id,
                'status_code' => $response->getStatusCode(),
                'body' => $response->getBody()
            ]);

            return response()->json(['failed' => 'failed'], 500);
        }

        $responseContent = json_decode($response->getBody()->getContents(), true);

        if (isset($responseContent['error_status_code']) and $responseContent['error_status_code'] == 1) {
            if ($addonTest = AddonTest::where('addon_id', '=', $addon->id)->get()->first()) {
                $addonTest->delete();
            }

            $addon->delete();

            return response()->json(['failed' => 'failed'], 500);
        }

        try {
            $sitesInfoCollection = CollectionFactory::createAddonSiteInfoCollection($responseContent);
        } catch (Throwable $exception) {
            $sitesInfoCollection = null;

            Log::error("\e[31mFailed to analyze addon. Error to create AddonSiteInfoCollection!\e[97m", [
                'addon_id' => $addon->id,
                'exception' => $exception->getMessage()
            ]);
            throw new Exception("AddonId: " . $addon->id . '.' . $exception->getMessage());
        }

        if ($sitesInfoCollection === null || $sitesInfoCollection->count() === 0) {
            Log::error("\e[31mFailed to analyze addon. Collection is empty!\e[97m", [
                'addon_id' => $addon->id
            ]);

            return response()->json(['failed' => 'failed'], 500);
        }

        /** @var SiteInfo $siteInfo */
        foreach ($sitesInfoCollection as $siteInfo) {
            $addon->sites()->sync([
                $siteInfo->getSiteId() => [
                    'content_scripts_count' => $siteInfo->getContentScriptsCount(),
                    'content_scripts_count_with_signs' => $siteInfo->getContentScriptsCountWithSigns(),
                    'scripts_info' => $siteInfo->getScriptsInfoCollection() !== null ?
                        serialize($siteInfo->getScriptsInfoCollection()->getArrayCopy())
                        : null
                ]
            ], false);
        }

        return response()->json(['success' => $responseContent], 200);
    }
}
