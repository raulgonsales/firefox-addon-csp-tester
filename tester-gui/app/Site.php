<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'addon_site');
    }
}
