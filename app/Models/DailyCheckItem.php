<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyCheckItem extends Model
{
    protected $guarded = [];

    public function daily_check()
    {
        return $this->belongsTo(DailyCheck::class);
    }
}
