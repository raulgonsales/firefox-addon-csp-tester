<?php set_time_limit(200);

require '../vendor/simple-html-dom/simple-html-dom/simple_html_dom.php';
require '../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$dotenv = Dotenv\Dotenv::create(__DIR__ . '/../');
$dotenv->load();

$base_host = 'https://addons.mozilla.org';
$addonsDir = __DIR__ . '/addons/';

$s3 = new Aws\S3\S3Client([
    'region' => env('AWS_DEFAULT_REGION'),
    'version' => 'latest',
    'credentials' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
    ]
]);

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => env('DB_CONNECTION'),
    'host' => env('DB_HOST'),
    'port' => env('DB_PORT'),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
]);
$capsule->setAsGlobal();
$capsule::table('addons')->orderBy('id')->chunk(100, function($addons) use ($base_host, $addonsDir, $s3) {
    foreach ($addons as $addon) {
        $addon_page = file_get_html($base_host . $addon->link);
        if (!$addon_page) {
            continue;
        }

        $addon_file_link = $addon_page->find('#redux-store-state', 0)->innertext;
        $addon_info = json_decode($addon_file_link, true)['versions']['byId'];

        $fileUrl = null;
        array_walk_recursive($addon_info, function ($item, $key) use (&$fileUrl) {
            if ($key === 'url' && strpos($item, 'addons.mozilla.org/firefox/downloads/file')) {
                $fileUrl = $item;
                return;
            }
        });

        if (
            is_null($fileUrl)
            || file_put_contents('./addons/' . $addon->file_name, fopen($fileUrl, 'r')) === false
        ) {
            continue;
        }

        if (!$s3->doesObjectExist(env('AWS_BUCKET'), 'addons-files/' . $addon->file_name)) {
            $s3->putObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key'    => 'addons-files/' . $addon->file_name,
                'Body' => fopen($addonsDir . $addon->file_name, 'r+')
            ]);

            $s3->waitUntil('ObjectExists', array(
                'Bucket' => env('AWS_BUCKET'),
                'Key'    => 'addons-files/' . $addon->file_name
            ));
        }

        if (file_exists($addonsDir . $addon->file_name)) {
            unlink($addonsDir . $addon->file_name);
        }
    }
    die();
});
