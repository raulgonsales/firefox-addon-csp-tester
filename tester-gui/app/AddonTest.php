<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddonTest extends Model
{
    protected $fillable = ['type_name', 'failed_test'];
    public $timestamps = false;
}
