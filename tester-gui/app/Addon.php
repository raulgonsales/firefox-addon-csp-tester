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

    public function addonTests()
    {
        return $this->hasMany(AddonTest::class);
    }
}
