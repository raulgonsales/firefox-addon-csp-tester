<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'addon_site');
    }

    public function relatedAddons()
    {
        return $this->belongsToMany(Addon::class, 'addon_site')->wherePivot('content_scripts_count', '>', 0)->withPivot('content_scripts_count');
    }
}
