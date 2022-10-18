<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriticalCheck extends Model
{
    protected $guarded = [];

    public function daily_check()
    {
        return $this->belongsTo(CriticalCaseP1::class,'critical_check_id');
    }
}
