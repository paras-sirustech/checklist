<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }
}
