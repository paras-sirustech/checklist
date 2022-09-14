<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function cluster()
    {
        return $this->belongsTo(Cluster::class, 'cluster_id');
    }

    public function ip_addresses()
    {
        return $this->hasMany(ShopIpAddress::class);
    }

    public function daily_checks()
    {
        return $this->hasMany(DailyCheck::class);
    }

    public function support_tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function actionable_tickets()
    {
        return $this->hasMany(SupportTicket::class)->whereIn('status', ['Open','In Progress']);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function branch_admins()
    {
        return $this->belongsToMany(User::class)->where('type', 'Branch Admin');
    }

    public function branch_managers()
    {
        return $this->belongsToMany(User::class)->where('type', 'Branch Manager');
    }

    public function getAttachedChecklistOptions()
    {
        unset($options);
        $checklist_options = optional($this->checklist)->items;
        if ($checklist_options && $checklist_options->count()>0) {
            foreach ($checklist_options as $option) {
                $options[$option->name] = $option->name;
            }
            return $options;
        }
    }
}
