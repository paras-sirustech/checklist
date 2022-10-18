<?php

namespace App\Nova\Metrics;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Metrics\Value;
use SaintSystems\Nova\LinkableMetrics\LinkableValue;

class SupportTicketsStatusInProgress extends Value
{
    use LinkableValue;

    public $name = 'In-Progress Tickets';

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
                $result = $this->count($request, SupportTicket::where('status', 'In Progress'));
            } else {
                $result = $this->count($request, SupportTicket::where('status', 'In Progress')->where('assigned_to', $request->user()->id));
            }
        } elseif ($request->user()->access_level == 'Shop Staff') {
            if ($request->range == 999) {
                $result = $this->count($request, SupportTicket::where('status', 'In Progress'));
            } else {
                $result = $this->count($request, SupportTicket::where('status', 'In Progress')->where('created_by', $request->user()->id));
            }
        } elseif ($request->user()->access_level == 'Cluster Manager') {
            if ($request->range == 999) {
                $result = $this->count($request, SupportTicket::where('status', 'In Progress'));
            } else {
                $result = $this->count($request, SupportTicket::where('status', 'In Progress')->where('shop_id', $request->user()->cluster_shop_ids()));
            }
        } else {
            if ($request->range == 999) {
                $result = $this->count($request, SupportTicket::where('status', 'In Progress'));
            } else {
                $result = $this->count($request, SupportTicket::where('status', 'In Progress')->where('assigned_to', $request->range));
            }
        }

        $filters = [
            [
                'class' => 'App\\Nova\\Filters\\TicketStatus',
                'value' => 'In Progress'
            ],
        ];

        $params = ['resourceName' => 'support-tickets'];
        $query = [
            'support-tickets_page' => 1,
            'support-tickets_filter' => base64_encode(json_encode($filters)),
        ];
        return $result->route('index', $params, $query);


//        if ($request->user()->access_level == 'Support Staff') {
//            $result = SupportTicket::where('status', 'In Progress')->where('assigned_to', $request->user()->id);
//        } elseif ($request->user()->access_level == 'Shop Staff') {
//            $result = SupportTicket::where('status', 'In Progress')->where('created_by', $request->user()->id);
//        } elseif ($request->user()->access_level == 'Cluster Manager') {
//            $result = SupportTicket::where('status', 'In Progress')->whereIn('shop_id', $request->user()->cluster_shop_ids());
//        } else {
//            $result = SupportTicket::where('status', 'In Progress');
//        }
//
//        if ($request->range == '-') {
//            $count = $result->count();
//        } else {
//            $count = $result->where('assigned_to', $request->range)->count();
//        }
//
//        return $this->result($count)->allowZeroResult();


//        $filters = [
//            [
//                'class' => 'App\\Nova\\Filters\\TicketStatus',
//                'value' => 'In Progress'
//            ],
//        ];
//
//        $params = ['resourceName' => 'support-tickets'];
//        $query = [
//            'support-tickets_page' => 1,
//            'support-tickets_filter' => base64_encode(json_encode($filters)),
//        ];
//        return $result->route('index', $params, $query);
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
//        return [
//            999 => 'All Time',
//            7 => '7 Days',
//            30 => '30 Days',
//            60 => '60 Days',
//            365 => '365 Days',
//            'MTD' => 'Month To Date',
//            'QTD' => 'Quarter To Date',
//            'YTD' => 'Year To Date',
//        ];
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
        return 'support-tickets-status-in-progress';
    }
}
