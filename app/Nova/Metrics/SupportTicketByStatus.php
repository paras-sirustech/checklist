<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use App\Models\SupportTicket;

class SupportTicketByStatus extends Partition
{
    public $name = 'Tickets by Status';
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, SupportTicket::where('status', '!=', 'Closed'), 'status')->colors([
            'Open' => 'red',
            'In Progress' => 'rgb(245, 87, 59)',
            'Closed' => '#6ab04c',
            'Resolved' => '#6ab04c',
        ]);
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
        return 'support-ticket-by-status';
    }
}
