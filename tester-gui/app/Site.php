<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'addon_site');
    }

    public function relatedAddonsWithScriptSigns()
    {
        return $this->belongsToMany(Addon::class, 'addon_site')
            ->wherePivot('content_scripts_count', '>', 0)
            ->wherePivot('content_scripts_count_with_signs', '>', 0)
            ->withPivot(['content_scripts_count', 'content_scripts_count_with_signs', 'scripts_info'])->orderByDesc('users_count');
    }
}
