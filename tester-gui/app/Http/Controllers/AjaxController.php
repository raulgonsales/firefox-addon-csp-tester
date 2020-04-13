<?php

namespace App\Http\Controllers;

use App\Addon;
use App\AddonTest;
use App\Models\Enum\TestTypesEnum;
use BadMethodCallException;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

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
        if (isset($request->addon_id)) {
            if (!($addon = Addon::find($request->addon_id))->exists()) {
                Log::error("\e[31mFailed to test addon. No record in Addons table in the database!", [
                    'addon_id' => $request->addon_id,
                    'test_type' => $testType
                ]);

                return false;
            }
        } else {
            Log::error("\e[31mFailed to test addon. Addon id required!", [
                'addon_id' => $request->addon_id,
                'test_type' => $testType
            ]);

            return false;
        }

        if (!$request->isMethod('post')) {
            Log::error("\e[31mFailed to test addon. Bad method call!", [
                'addon_id' => $addon->id,
                'test_type' => $testType
            ]);

            $addon->addonTests()->create([
                'type_name' => $testType
            ]);

            throw new BadMethodCallException();
        }

        try {
            $testType = new TestTypesEnum($testType);
        } catch (InvalidEnumMemberException $exception) {
            Log::error("\e[31mFailed to test addon. Bad test type given!\e[97m", [
                'addon_id' => $addon->id,
                'test_type' => $testType
            ]);

            $addon->addonTests()->create([
                'type_name' => $testType,
                'failed_test' => true
            ]);

            throw new InvalidArgumentException();
        }

        $existedNotFailedAddonTests = $addon->addonTests()
            ->where('type_name', '=', $testType->value)
            ->whereNull('failed_test')
            ->get();
        if ($existedNotFailedAddonTests->count() > 0) {
            Log::debug('Addon already tested successfully!', [
                'addon_id' => $addon->id,
                'test_type' => $testType
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
                    ]
                ]
            );
        } catch (Exception $exception) {
            Log::error("\e[31mFailed to test addon. Bad response from backend service!\e[97m", [
                'addon_id' => $addon->id,
                'test_type' => $testType,
                'exception' => $exception->getMessage()
            ]);

            $addon->addonTests()->create([
                'type_name' => $testType,
                'failed_test' => true
            ]);

            return false;
        }

        if ($response->getStatusCode() === 200) {
            $allAddonTests = $addon->addonTests()->where('type_name', '=', $testType->value)->get();

            /** @var AddonTest $test */
            foreach ($allAddonTests as $test) {
                $test->delete();
            }
            $addon->addonTests()->create([
                'type_name' => $testType->value
            ]);

            return $response->getBody();
        } else {
            Log::error("\e[31mFailed to test addon. Bad response from backend service!\e[97m", [
                'addon_id' => $addon->id,
                'test_type' => $testType,
                'status_code' => $response->getStatusCode(),
                'body' => $response->getBody()
            ]);

            $addon->addonTests()->create([
                'type_name' => $testType,
                'failed_test' => true
            ]);
        }

        return false;
    }
}
