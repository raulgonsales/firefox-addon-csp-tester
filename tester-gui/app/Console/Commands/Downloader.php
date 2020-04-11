<?php

namespace App\Console\Commands;

use Aws\S3\S3Client;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\StreamInterface;

require_once __DIR__ . '/../../../vendor/simple-html-dom/simple-html-dom/simple_html_dom.php';

class Downloader extends Command
{
    const STORE_BASE_HOST = 'https://addons.mozilla.org';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'downloader';

    /**
     * @var S3Client
     */
    protected $s3Client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->s3Client = new S3Client([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);
    }

    /**
     * @param  string  $addonLink
     * @return \Psr\Http\Message\StreamInterface|null
     */
    protected function getAddonsPageHTML(string $addonLink): ?StreamInterface
    {
        $client = new Client();

        try {
            $httpResult = $client->request(
                'GET',
                self::STORE_BASE_HOST . $addonLink,
                [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0',
                    ]
                ]
            );
        } catch (ClientException $exception) {
            return null;
        }

        return $httpResult->getBody();
    }

    /**
     * @param  string  $filename
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function S3PutObject(string $filename):void
    {
        $this->s3Client->putObject([
            'Bucket' => env('AWS_BUCKET'),
            'Key'    => 'addons-files/' . $filename,
            'Body' => Storage::disk('local')->get('/addons/' . $filename)
        ]);

        $this->s3Client->waitUntil('ObjectExists', array(
            'Bucket' => env('AWS_BUCKET'),
            'Key'    => 'addons-files/' . $filename
        ));
    }

    /**
     * @param  string  $filename
     * @return bool
     */
    protected function removeFromLocal(string $filename):bool
    {
        if (Storage::disk('local')->exists('/addons/' . $filename)) {
            Storage::disk('local')->delete('/addons/' . $filename);
            return true;
        } else {
            return false;
        }
    }
}
