<?php
set_time_limit(200);
require '../vendor/simple-html-dom/simple-html-dom/simple_html_dom.php';
require '../vendor/autoload.php';

$base_host = 'https://addons.mozilla.org';
$base_search_link = $base_host . '/en-US/firefox/search/?';
$type = 'extension';
$addons_html = file_get_html($base_search_link . http_build_query([
		'page' => $page = 1,
		'type' => $type
	]));
$scrapAddonFile = true;

$pagination = $addons_html->find('div.Paginate', 0);

$pages_count = 1;
if (!empty($pagination)) {
	$pages_number_text = $pagination->find('div.Paginate-page-number', 0)->innertext;
	$pages_count = (integer) explode(' ', $pages_number_text)[3];
}

$addons_links = [];
$search_result_page = $addons_html;
do {
	if ($page > 1) {
		$search_result_page = file_get_html($base_search_link . http_build_query([
				'page' => $page,
				'type' => $type
			]));
	}

	$search_result_list = $search_result_page->find('li.SearchResult');

	$links_batch = [];
	foreach ($search_result_list as $list_item) {
		$addon_name = $list_item->find('a.SearchResult-link', 0)->innertext;
		$addon_link = $list_item->find('a.SearchResult-link', 0)->getAttribute('href');

		if ($scrapAddonFile) {
			$addon_page = file_get_html($base_host . $addon_link);
			$addon_file_link = $addon_page->find('#redux-store-state', 0)->innertext;

			$addon_info = json_decode($addon_file_link, true)['versions']['byId'];

			$fileUrl = null;
			array_walk_recursive($addon_info, function ($item, $key) use (&$fileUrl) {
				if ($key === 'url' && strpos($item, 'addons.mozilla.org/firefox/downloads/file')) {
					$fileUrl = $item;
					return;
				}
			});

			if (is_null($fileUrl)) {
				continue;
			}

			$fileName = strtolower(implode('_', explode(' ', preg_replace('/[^\da-z ]/i', '', $addon_name)))) . '_' . uniqid() . '.xpi';
			file_put_contents('./addons/' . $fileName, fopen($fileUrl, 'r'));
		}

		$addon_info = [
			'name' => $list_item->find('a.SearchResult-link', 0)->innertext,
			'link' => $addon_link,
			'file_name' => strtolower(implode('_', explode(' ', preg_replace('/[^\da-z ]/i', '', $addon_name)))) . '_' . md5($addon_link) . '.xpi',
			'img_name' => $list_item->find('img.SearchResult-icon', 0)->getAttribute('src'),
			'users_count' => (integer)preg_replace("/[^0-9]/", '', $list_item->find('span.SearchResult-users-text', 0)->innertext),
			'firefox_recommend' => $list_item->find('span.RecommendedBadge-label', 0) ? true : false
		];

		$links_batch[] = $addon_info;
	}

	try {
        store_to_database($links_batch);
    } catch(Exception $e) {
        echo $e->getMessage() . '<br>';
        $not_stored[] = $links_batch;
        $fp = fopen('not_stored.json', 'w');
        fwrite($fp, '');
        fwrite($fp, json_encode($not_stored, JSON_PRETTY_PRINT));
        fclose($fp);
    }

	echo $page . '<br>';
	$page++;
} while ($page <= 747);

function store_to_database(array $links) {
	$url = 'nginx/api/store-links';

	try {
		$client = new \GuzzleHttp\Client();
		$response = $client->post($url, [
			\GuzzleHttp\RequestOptions::JSON => $links
		]);

		print_r($response->getBody()->getContents());
	} catch (Exception $e) {
        throw $e;
	}
}
