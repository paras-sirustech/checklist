<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;

class Leave extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'user_id', 'from_date', 'to_date', 'note'
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
