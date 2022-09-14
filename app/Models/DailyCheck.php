<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyCheck extends Model
{
    protected $guarded = [];

    protected $casts = [
        'checking_date' => 'date',
        //'checklist_item_status' => 'array',
        'is_submission_complete' => 'boolean',
        'completed_at' => 'datetime',
    ];

    protected $dates = [
        'checking_date',
        'completed_at',
        'created_at',
        'updated_at',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    public function items()
    {
        return $this->hasMany(DailyCheckItem::class);
    }

    public function support_tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function getChecklistItemStatusAttribute()
    {
        $status = [];
        foreach($this->checklist->items as $item){
            $status[$item->name] = false;
            unset($daily_check_item);
            $daily_check_item = DailyCheckItem::where('daily_check_id',$this->id)->where('checklist_item_id',$item->id)->where('status','Okay')->first();
            if($daily_check_item){
                $status[$item->name] = true;
            }
        }
        return $status;
    }

    public function setShopIdAttribute($value)
    {
        $this->attributes['shop_id'] = $value;
        $this->attributes['submitted_by'] = auth()->user()->id;
        $this->attributes['checklist_id'] = 1;
    }
}