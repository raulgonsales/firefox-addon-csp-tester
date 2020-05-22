<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CspReport extends Model
{
	use SoftDeletes;

	public function getByTestType()
    {

    }

    public function addon()
    {
        return $this->hasOne(Addon::class);
    }
}
