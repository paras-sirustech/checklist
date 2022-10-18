<?php

namespace App\Nova\Metrics;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Insenseanalytics\NovaBarMetrics\BarChartMetric;

class SupportTicketsBarChart extends BarChartMetric
{
    public $name = "New Tickets";

    /**
     * Calculate the value of the metric.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        if (Auth::user()->access_level == "Admin") {
            $support = SupportTicket::select('support_tickets.*', 'users.name')->join('users', 'users.id', '=', 'support_tickets.assigned_to');
        } else {
            $support = SupportTicket::select('support_tickets.*', 'users.name')->join('users', 'users.id', '=', 'support_tickets.assigned_to')->where('support_tickets.assigned_to', Auth::user()->id);
        }
        return $this->count($request, $support, 'name');
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'support-tickets-bar-chart';
    }
}
