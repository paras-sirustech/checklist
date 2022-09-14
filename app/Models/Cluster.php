<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cluster extends Model
{
    protected $guarded = [];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}
