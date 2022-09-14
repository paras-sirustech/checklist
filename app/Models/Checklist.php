<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function items()
    {
        return $this->hasMany(ChecklistItem::class)->where('priority', '!=', 'P1')->orderBy('priority', 'ASC')->orderBy('name', 'ASC');
    }

    public function allItems()
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('priority', 'ASC')->orderBy('name', 'ASC');
    }
}
