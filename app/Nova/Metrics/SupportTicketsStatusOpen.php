<?php

namespace App\Nova\Metrics;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Value;
use SaintSystems\Nova\LinkableMetrics\LinkableValue;

class SupportTicketsStatusOpen extends Value
{
    use LinkableValue;
    public $name = 'Open Tickets';
    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        if ($request->user()->access_level=='Support Staff') {
            $result = $this->count($request, SupportTicket::where('status', 'Open')->where('assigned_to', $request->user()->id));
        } elseif ($request->user()->access_level=='Shop Staff') {
            $result = $this->count($request, SupportTicket::where('status', 'Open')->where('created_by', $request->user()->id));
        } elseif ($request->user()->access_level=='Cluster Manager') {
            $result = $this->count($request, SupportTicket::where('status', 'Open')->whereIn('shop_id', $request->user()->cluster_shop_ids()));
        } else {
            $result = $this->count($request, SupportTicket::where('status', 'Open'));
        }


        $filters = [
            [
                'class' => 'App\\Nova\\Filters\\TicketStatus',
                'value' => 'Open'
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
        return [
            999 => 'All Time',
            7 => '7 Days',
            30 => '30 Days',
            60 => '60 Days',
            365 => '365 Days',
            'MTD' => 'Month To Date',
            'QTD' => 'Quarter To Date',
            'YTD' => 'Year To Date',
        ];
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
        return 'support-tickets-status-open';
    }
}
