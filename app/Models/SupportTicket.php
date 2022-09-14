<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;
use Notification;
use App\Notifications\TicketEscalation;

class SupportTicket extends Model
{
    use Actionable;

    protected $guarded = [];
    protected $appends = ['url'];

    protected $casts = [
        'due_date' => 'date',
        'resolved_date' => 'date',
        'last_support_team_ticket_update' => 'date',
        'l1_escalation_at' => 'date',
        'l2_escalation_at' => 'date',
        'l3_escalation_at' => 'date',
    ];

    protected $dates = [
        'due_date',
        'resolved_date',
        'last_support_team_ticket_update',
        'l1_escalation_at',
        'l2_escalation_at',
        'l3_escalation_at',
        'created_at',
        'updated_at',
    ];

    public function replies()
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id');
    }

    public function escalations()
    {
        return $this->hasMany(SupportTicketEscalation::class, 'ticket_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    public function checklist_item()
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    public function setShopIdAttribute($value)
    {
        $this->attributes['shop_id'] = $value;
        $this->attributes['created_by'] = auth()->user()->id;
    }

    public function getUrlAttribute()
    {
        return config('app.url') . '/app/resources/support-tickets/' . $this->id;
    }

    public function email_cc()
    {
        $email_cc[] = 'RetailSupport@ooredoo.qa';
        if ($this->creator) {
            $email_cc[] = $this->creator->email;
        }
        if ($this->assignee) {
            $email_cc[] = $this->assignee->email;
        }
        $cluster_manager = optional(optional($this->shop)->cluster)->assignee;
        if ($cluster_manager) {
            $email_cc[] = $cluster_manager->email;
        }
        return $email_cc;
    }

    public function scopePriorityChecklistItem($query, $type)
    {
        return $query->whereHas('checklist_item', function ($q) use ($type) {
            $q->where('priority', $type);
        });
    }

    public function isEligibileForEscalation($type, $level)
    {
        $escalation['L1'] = [1, 24];
        $escalation['L2'] = [2, 36];
        $escalation['L3'] = [4, 60];

        if ($type=='P1') {
            $threshold = $escalation[$level][0];
        } else {
            $threshold = $escalation[$level][1];
        }

        if ($level=='L1') {
            $last_escalation_at = $this->l1_escalation_at;
        } elseif ($level=='L2') {
            $last_escalation_at = $this->l2_escalation_at;
        } elseif ($level=='L3') {
            $last_escalation_at = $this->l3_escalation_at;
        }

        if (is_null($last_escalation_at) && $this->status!='Closed' && $this->status!='Resolved') {
            if ((is_null($this->last_support_team_ticket_update) || $this->last_support_team_ticket_update->diffInHours(now())>=$threshold) && $this->created_at->diffInHours(now())>=$threshold) {
                return true;
            }
        }
        return false;
    }

    public function escalate($type, $level)
    {
        $support_staff = optional($this->assignee)->email;
        $shop_manager = null;
        $cluster_manager = null;
        if ($this->shop) {
            $shop_manager = optional($this->shop->branch_managers->first())->email;
            $cluster_manager = optional(optional($this->shop->cluster)->assignee)->email;
        }
        $l2escalation_email = config('services.l2escalation_email');
        $l3escalation_email = config('services.l3escalation_email');
        $franchisee_manager = config('services.franchisee_manager');
        $retail_head_email = config('services.retail_head_email');

        $to_email=[];

        if ($type=='P1') {
            if ($level=='L1') {
                $to_email[] = optional($this->assignee)->email;
                $this->l1_escalation_at = now();
                $this->save();
            } elseif ($level=='L2') {
                $to_email[] = config('services.l2escalation_email');
                if ($shop_manager!='') {
                    $to_email[] = $shop_manager;
                }
                if ($cluster_manager!='') {
                    $to_email[] = $cluster_manager;
                }
                $this->l2_escalation_at = now();
                $this->save();
            } elseif ($level=='L3') {
                $to_email[] = config('services.l3escalation_email');
                $to_email[] = config('services.retail_head_email');
                $this->l3_escalation_at = now();
                $this->save();
            }
        } else {
            if ($level=='L1') {
                $to_email[] = optional($this->assignee)->email;
                $this->l1_escalation_at = now();
                $this->save();
            } elseif ($level=='L2') {
                $to_email[] = config('services.l2escalation_email');
                if ($shop_manager!='') {
                    $to_email[] = $shop_manager;
                }
                $this->l2_escalation_at = now();
                $this->save();
            } elseif ($level=='L3') {
                $to_email[] = config('services.l3escalation_email');
                if ($cluster_manager!='') {
                    $to_email[] = $cluster_manager;
                }
                $this->l3_escalation_at = now();
                $this->save();
            }
        }

        if (count($to_email)>0) {
            for ($i=0;$i<count($to_email);$i++) {
                $this->escalations()->create(['email'=>$to_email[$i],'type'=>$type, 'level'=>$level]);
            }

            Notification::route('mail', $to_email)->notify(new TicketEscalation($this, $type));
            $log = 'Ticket: ' . $this->id . ' -  ' . $type . ' Escalation to ' .$level . ' - ' . json_encode($to_email, false);
            info($log);
            return $log;
        }
    }
}
