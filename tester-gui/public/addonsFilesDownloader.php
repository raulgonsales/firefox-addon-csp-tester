<?php set_time_limit(600);

require __DIR__ . '/../vendor/simple-html-dom/simple-html-dom/simple_html_dom.php';
require __DIR__ . '/../vendor/autoload.php';

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
    'charset' => 'utf8',
]);
$capsule->setAsGlobal();

$chunkCounter = 0;
$not_stored = [];
$capsule::table('addons')->orderBy('id')->chunk(100, function($addons) use ($base_host, $addonsDir, $s3, &$chunkCounter, &$not_stored) {
    try {
        echo "\n\n\e[34m======================= CHUNK $chunkCounter ======================== \e[97m\n\n";
        foreach ($addons as $addon) {
            echo "+++++++++++++ Addon \e[93m" . $addon->name . " \e[97m with id \e[93m $addon->id \e[97m parsing started\n";

            if ($s3->doesObjectExist(env('AWS_BUCKET'), 'addons-files/' . $addon->file_name)) {
                echo "\e[31m File already exists on S3 \e[97m\n\n";
                continue;
            }

            $addon_page = file_get_html($base_host . $addon->link);
            if (!$addon_page) {
                echo "\e[31m Failed to get addon's page \e[97m\n";
                $not_stored[] = $addon->id;
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
                || file_put_contents(__DIR__ . '/addons/' . $addon->file_name, fopen($fileUrl, 'r')) === false
            ) {
                $not_stored[] = $addon->id;
                continue;
            }

            echo "Original filename -  $fileUrl \e[97m\n";
            echo "\e[32m File $addon->file_name downloaded to local \e[97m\n";

            $s3->putObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key'    => 'addons-files/' . $addon->file_name,
                'Body' => fopen($addonsDir . $addon->file_name, 'r+')
            ]);

            $s3->waitUntil('ObjectExists', array(
                'Bucket' => env('AWS_BUCKET'),
                'Key'    => 'addons-files/' . $addon->file_name
            ));

            echo "\e[32m File $addon->file_name uploaded into S3 \e[97m\n";

            if (file_exists($addonsDir . $addon->file_name)) {
                unlink($addonsDir . $addon->file_name);
                echo "\e[32m File successfully removed from local";
            } else {
                echo "\e[31m Cannot remove file from local, file not exists";
            }

            echo "\e[97m\n";
        }

        echo "\e[97m Chunk finished\n";

        $chunkCounter++;
    } catch(Throwable $exception) {
        echo $exception->getMessage() . "\n" . $exception->getTraceAsString();
        die();
    }
});

if (!empty($not_stored)) {
    $fp = fopen('not_stored.json', 'w');
    fwrite($fp, json_encode($not_stored, JSON_PRETTY_PRINT));
    fclose($fp);
}
