<?php

namespace App\Nova\Metrics;

use App\Models\SupportTicket;
use App\Models\User;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class SupportTicketsUnresolvedByUsers extends Value
{
    public $name = 'Unresolved Tickets';

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $support_tickets = SupportTicket::whereNotIn('status', ['Closed','Resolved']);

        if ($request->range=='-') {
            $count = $support_tickets->count();
        } else {
            $count = $support_tickets->where('assigned_to', $request->range)->count();
        }
        
        return $this->result($count)->allowZeroResult();
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        $ranges['-'] = 'All';
        $users = User::whereIn('access_level', ['Support Staff','Admin'])->get();

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
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'support-tickets-unresolved-by-users';
    }
}
