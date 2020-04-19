<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    public $timestamps = false;

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'addon_site');
    }

    public function cspReports()
    {
        return $this->hasMany(CspReport::class);
    }

    public function cspReportsBasic()
    {
        return $this->hasMany(CspReport::class)->select(['id', 'addon_id', 'source_file']);
    }

    public function addonTests()
    {
        return $this->hasMany(AddonTest::class);
    }

    public static function addonsWithExistedCspReports(string $testType)
    {
        return self::select(['addons.id', 'name'])
            ->leftJoin('csp_reports', 'csp_reports.addon_id', '=', 'addons.id')
            ->where('test_type', '=', 'on-start-test')
            ->whereNotNull('csp_reports.id')
            ->distinct()
            ->get()
            ->load('cspReportsBasic');
    }
}
