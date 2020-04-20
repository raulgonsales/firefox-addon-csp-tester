<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SitesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sites')->insert([
            ['site_name' => 'All sites', 'matching_url' => '<all_urls>'],
            ['site_name' => 'All sites', 'matching_url' => '*://*/*'],
            ['site_name' => 'All sites HTTP', 'matching_url' => 'http://*/*'],
            ['site_name' => 'All sites HTTPS', 'matching_url' => 'https://*/*'],
            ['site_name' => 'All files', 'matching_url' => 'file:///*'],
            ['site_name' => 'Google', 'matching_url' => 'https://www.google.com'],
            ['site_name' => 'Twitter', 'matching_url' => 'https://twitter.com'],
            ['site_name' => 'Facebook', 'matching_url' => 'https://www.facebook.com'],
            ['site_name' => 'LinkedIn', 'matching_url' => 'https://www.linkedin.com'],
            ['site_name' => 'Ebay', 'matching_url' => 'https://www.ebay.com'],
            ['site_name' => 'Netflix', 'matching_url' => 'https://www.netflix.com'],
            ['site_name' => 'Indeed', 'matching_url' => 'indeed.com'],
            ['site_name' => 'IMBD', 'matching_url' => 'https://www.imdb.com'],
            ['site_name' => 'ESPN', 'matching_url' => 'https://www.espn.com'],
            ['site_name' => 'CNN', 'matching_url' => 'cnn.com'],
            ['site_name' => 'BBC', 'matching_url' => 'https://www.bbc.com/'],
            ['site_name' => 'Pinterest', 'matching_url' => 'https://www.pinterest.com'],
            ['site_name' => 'Walmart', 'matching_url' => 'https://www.walmart.com'],
            ['site_name' => 'Booking', 'matching_url' => 'https://www.booking.com'],
            ['site_name' => 'AirBnb', 'matching_url' => 'https://www.airbnb.com'],
            ['site_name' => 'Amazon', 'matching_url' => 'https://www.amazon.com'],
            ['site_name' => 'Mozilla', 'matching_url' => 'https://www.mozilla.org'],
            ['site_name' => 'Vk.com', 'matching_url' => 'https://vk.com'],
            ['site_name' => 'Youtube', 'matching_url' => 'https://www.youtube.com'],
            ['site_name' => 'Reddit', 'matching_url' => 'https://www.reddit.com'],
            ['site_name' => 'Wikipedia', 'matching_url' => 'https://www.wikipedia.org'],
            ['site_name' => 'Twitch', 'matching_url' => 'https://www.twitch.tv'],
            ['site_name' => 'Stackoverflow', 'matching_url' => 'https://stackoverflow.com'],
            ['site_name' => 'Github', 'matching_url' => 'https://github.com']
        ]);
    }
}
