<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopIpAddress extends Model
{
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
