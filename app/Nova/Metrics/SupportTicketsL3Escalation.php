<?php

namespace App\Nova\Metrics;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Metrics\Value;
use SaintSystems\Nova\LinkableMetrics\LinkableValue;

class SupportTicketsL3Escalation extends Value
{
    use LinkableValue;

    public $name = 'L3 Escalated Tickets';

    /**
     * Calculate the value of the metric.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        if ($request->user()->access_level == 'Support Staff') {
            if ($request->range == 999) {
                $result = $this->count($request, SupportTicket::whereNotNull('l3_escalation_at')->whereNotIn('status', ['Closed', 'Resolved']));
            } else {
                $result = $this->count($request, SupportTicket::whereNotNull('l3_escalation_at')->whereNotIn('status', ['Closed', 'Resolved'])->where('assigned_to', $request->user()->id));
            }
        } elseif ($request->user()->access_level == 'Shop Staff') {
            if ($request->range == 999) {
                $result = $this->count($request, SupportTicket::whereNotNull('l3_escalation_at')->whereNotIn('status', ['Closed', 'Resolved']));
            } else {
                $result = $this->count($request, SupportTicket::whereNotNull('l3_escalation_at')->whereNotIn('status', ['Closed', 'Resolved'])->where('created_by', $request->user()->id));
            }
        } elseif ($request->user()->access_level == 'Cluster Manager') {
            if ($request->range == 999) {
                $result = $this->count($request, SupportTicket::whereNotNull('l3_escalation_at')->whereNotIn('status', ['Closed', 'Resolved']));
            } else {
                $result = $this->count($request, SupportTicket::whereNotNull('l3_escalation_at')->whereNotIn('status', ['Closed', 'Resolved'])->where('shop_id', $request->user()->cluster_shop_ids()));
            }
        } else {
            if ($request->range == 999) {
                $result = $this->count($request, SupportTicket::whereNotNull('l3_escalation_at')->whereNotIn('status', ['Closed', 'Resolved']));
            } else {
                $result = $this->count($request, SupportTicket::whereNotNull('l3_escalation_at')->whereNotIn('status', ['Closed', 'Resolved'])->where('assigned_to', $request->range));
            }
        }

        $filters = [
            [
                'class' => 'App\\Nova\\Filters\\TicketEscalation',
                'value' => 'L3 Escalation'
            ],
            [
                'class' => 'App\\Nova\\Filters\\TicketStatus',
                'value' => 'Unresolved'
            ],
        ];

        $params = ['resourceName' => 'support-tickets'];
        $query = [
            'support-tickets_page' => 1,
            'support-tickets_filter' => base64_encode(json_encode($filters)),
        ];
        return $result->route('index', $params, $query);
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        if (Auth::user()->access_level == "Admin") {
            $ranges[999] = 'All';
            $users = User::get();
        } else {
            $users = User::where('id', Auth::user()->id)->get();
        }

        foreach ($users as $user) {
            $ranges[$user->id] = $user->name;
        }
        return $ranges;
//        return [
//            9999 => 'All Time',
//            1 => 'Last 24 Hours',
//            7 => '7 Days',
//            30 => '30 Days',
//            60 => '60 Days',
//            365 => '365 Days',
//            'MTD' => 'Month To Date',
//            'QTD' => 'Quarter To Date',
//            'YTD' => 'Year To Date',
//        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        //return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'support-tickets-l3-escalations';
    }
}
