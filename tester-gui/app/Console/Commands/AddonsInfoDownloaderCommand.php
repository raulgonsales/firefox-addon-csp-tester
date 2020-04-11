<?php

namespace App\Console\Commands;

use App\Addon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class AddonsInfoDownloaderCommand extends Downloader
{
    const RESOURCE_TYPE = 'extension';
    const SORT_TYPE = 'updated';
    const BASE_SEARCH_LINK = self::STORE_BASE_HOST . '/en-US/firefox/search/?';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'downloader:addons-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Downloads addon's information from Mozilla Firefox store.";

    /** @var integer */
    private $startPage;

    /** @var integer */
    private $finalPage = 0;

    /** @var bool */
    private $downloadFiles;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->startPage = 1;
        $this->finalPage = 753;
        $this->downloadFiles = true;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $page = $this->startPage;

        echo "\e[97mStarted downloading addon's information from the store\n";

        $addons_html = file_get_html(self::BASE_SEARCH_LINK . http_build_query([
            'page' => $page,
            'type' => self::RESOURCE_TYPE,
            'sort' => self::SORT_TYPE
        ]));

        if (!$addons_html) {
            echo "\e[31mFailed to get first page with addons \e[97m\n";
        }

        $pagination = $addons_html->find('div.Paginate', 0);

        if ($this->finalPage === 0) {
            if (!empty($pagination)) {
                $pages_number_text = $pagination->find('div.Paginate-page-number', 0)->innertext;
                $this->finalPage = (integer) explode(' ', $pages_number_text)[3];
            }
        }

        echo "\e[97mFinal page is $this->finalPage\n";

        $not_stored = [];
        $search_result_page = $addons_html;
        do {
            echo "\n\n\e[34m======================= Page $page ======================== \e[97m\n\n";
            if ($page > $this->startPage) {
                $search_result_page = file_get_html(self::BASE_SEARCH_LINK . http_build_query([
                    'page' => $page,
                    'type' => self::RESOURCE_TYPE,
                    'sort' => self::SORT_TYPE
                ]));

                if (!$search_result_page) {
                    echo "\e[31mFailed to get \e[93m$page\e[97m page with addons \e[97m\n";
                }
            }

            $search_result_list = $search_result_page->find('li.SearchResult');

            /** @var \simple_html_dom $list_item */
            foreach ($search_result_list as $list_item) {
                $addon_name = $list_item->find('a.SearchResult-link', 0)->innertext;
                $addon_link = $list_item->find('a.SearchResult-link', 0)->getAttribute('href');
                $filename = strtolower(implode('_', explode(' ', preg_replace('/[^\da-z ]/i', '', $addon_name)))) . '_' . md5($addon_link) . '.xpi';
                echo "\n\n\e[97m+++++++++++++++ Addon \e[93m$addon_name\e[97m +++++++++++++++ \e[97m\n\n";
                echo "    Addon link: \e[93m$addon_link \e[97m\n";
                echo "    File name: \e[93m$filename \e[97m\n";

                if ($this->downloadFiles) {
                    if ($this->s3Client->doesObjectExist(env('AWS_BUCKET'), 'addons-files/' . $filename)) {
                        echo "\e[31m    File already exists on S3 \e[97m\n\n";
                        goto stop_file_downloading;
                    }

                    $addon_page = str_get_html($this->getAddonsPageHTML($addon_link));
                    if (!$addon_page) {
                        echo "\e[31m    Failed to get addon's page \e[97m\n";
                        $not_stored[] = $addon_link;
                        continue;
                    }

                    $addon_file_link = $addon_page->find('.AMInstallButton-button', 0)->href;

                    if (
                        !$addon_file_link
                        || Storage::disk('local')->put('/addons/' . $filename, fopen($addon_file_link, 'r')) === false
                    ) {
                        $not_stored[] = $addon_link;
                        echo "\e[31m    Failed to get original link for file \e[97m\n";
                        continue;
                    }

                    echo "    Original filename -  $addon_file_link \e[97m\n";
                    echo "\e[32m    File $filename downloaded to local \e[97m\n";

                    try {
                        $this->S3PutObject($filename);
                        echo "\e[32m    File $filename uploaded into S3 \e[97m\n";
                    } catch (FileNotFoundException $exception) {
                        $not_stored[] = $addon_link;
                        echo "\e[31m    " . $exception->getMessage() . " \e[97m\n";
                        continue;
                    }

                    if ($this->removeFromLocal($filename)) {
                        echo "\e[32m    File successfully removed from local\n";
                    } else {
                        echo "\e[31m    Cannot remove file from local, file not exists\n";
                    }
                }

                stop_file_downloading:
                try {
                    if (Addon::where('file_name', '=', $filename)->exists()) {
                        echo "\e[31m    Addon record already exists \e[97m\n";
                        continue;
                    }

                    Addon::insert([
                        'name' => $list_item->find('a.SearchResult-link', 0)->innertext,
                        'link' => $addon_link,
                        'file_name' => $filename,
                        'img_name' => $list_item->find('img.SearchResult-icon', 0)->getAttribute('src'),
                        'users_count' => (integer)preg_replace("/[^0-9]/", '', $list_item->find('span.SearchResult-users-text', 0)->innertext),
                        'firefox_recommend' => $list_item->find('span.RecommendedBadge-label', 0) ? true : false
                    ]);

                    echo "\e[32m    Addon information successfully inserted into database \e[97m";
                } catch(Throwable $exception) {
                    echo "\e[31m    Failed to insert addon into database." . $exception->getMessage() . "\e[97m\n";
                    $not_stored[] = $addon_link;
                    continue;
                }

                echo "\e[97m\n";
            }

            echo "\e[97mChunk finished\n";
            $page++;
        } while ($page <= $this->finalPage);

        if (!empty($not_stored)) {
            Storage::disk('local')->put(
                '/addons/not_stored_addons.json',
                json_encode($not_stored, JSON_PRETTY_PRINT)
            );
        }

        return true;
    }
}
