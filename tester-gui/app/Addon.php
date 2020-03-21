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
}
