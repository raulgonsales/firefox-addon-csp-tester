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
            ['site_name' => 'Google', 'matching_url' => 'google.com'],
            ['site_name' => 'Facebook', 'matching_url' => 'facebook.com'],
            ['site_name' => 'Mozilla', 'matching_url' => 'mozilla.com'],
            ['site_name' => 'Vk.com', 'matching_url' => 'vk.com'],
            ['site_name' => 'Youtube', 'matching_url' => 'youtube.com'],
            ['site_name' => 'Reddit', 'matching_url' => 'reddit.com']
        ]);
    }
}
