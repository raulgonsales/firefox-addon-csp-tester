<?php

namespace App\Console\Commands;

use App\Addon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

final class AddonsFilesDownloaderCommand extends Downloader
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'downloader:addons-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Downloads extension files from the AMO.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $chunkCounter = 0;
        $not_stored = [];
        Addon::chunk(100, function($addons) use (&$chunkCounter, &$not_stored) {
            echo "\n\n\e[34m======================= CHUNK $chunkCounter ======================== \e[97m\n\n";

            foreach ($addons as $addon) {
                echo "+++++++++++++ Addon \e[93m" . $addon->name . " \e[97m with id \e[93m $addon->id \e[97m parsing started\n";
                echo "\e[97m    Filename - \e[93m $addon->file_name \e[97m\n";

                if ($this->s3Client->doesObjectExist(env('AWS_BUCKET'), 'addons-files/' . $addon->file_name)) {
                    echo "\e[31m    File already exists on S3 \e[97m\n\n";
                    continue;
                }

                $addon_page = str_get_html($this->getAddonsPageHTML($addon->link));

                if (!$addon_page) {
                    echo "\e[31m    Failed to get addon's page \e[97m\n";
                    Addon::find($addon->id)->delete();
                    echo "\e[31m    Addon was successfully removed from database \e[97m\n";
                    $not_stored[] = $addon->id;
                    continue;
                }

                $addon_file_link = $addon_page->find('.AMInstallButton-button', 0)->href;

                if (
                    !$addon_file_link
                    || Storage::disk('local')->put('/addons/' . $addon->file_name, fopen($addon_file_link, 'r')) === false
                ) {
                    $not_stored[] = $addon->id;
                    echo "\e[31m    Failed to get original link for file \e[97m\n";
                    continue;
                }

                echo "    Original filename -  $addon_file_link \e[97m\n";
                echo "\e[32m    File $addon->file_name downloaded to local \e[97m\n";

                try {
                    $this->S3PutObject($addon->file_name);
                } catch (FileNotFoundException $exception) {
                    $not_stored[] = $addon->id;
                    echo "\e[31m    " . $exception->getMessage() . " \e[97m\n";
                    continue;
                }

                echo "\e[32m    File $addon->file_name uploaded into S3 \e[97m\n";

                if ($this->removeFromLocal($addon->file_name)) {
                    echo "\e[32m    File successfully removed from local";
                } else {
                    echo "\e[31m    Cannot remove file from local, file not exists";
                }

                echo "\e[97m\n";
            }

            echo "\e[97m    Chunk finished\n";

            $chunkCounter++;

            if ($chunkCounter == 2) {
                if (!empty($not_stored)) {
                    Storage::disk('local')->put(
                        '/addons/not_stored_files.json',
                        json_encode($not_stored, JSON_PRETTY_PRINT)
                    );
                }
                die();
            }
        });

        return true;
    }
}
