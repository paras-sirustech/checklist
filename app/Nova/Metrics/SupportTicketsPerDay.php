<?php

namespace App\Nova\Metrics;

use App\Models\SupportTicket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;

class SupportTicketsPerDay extends Trend
{
    public $name = 'New Tickets';

    /**
     * Calculate the value of the metric.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        return $this->countByDays($request, SupportTicket::class);
//        if ($request->range == '-') {
//            $count = SupportTicket::where('created_at', Carbon::today()->toDateString())->count();
//        } else {
//            $count = SupportTicket::where(['assigned_to' => $request->range, 'created_at' Carbon::today()->toDateString()])->count();
//        }
//        return $this->result($count);
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
//        $ranges['-'] = 'All';
//        $users = User::get();
//
//        foreach ($users as $user) {
//            $ranges[$user->id] = $user->name;
//        }
//        return $ranges;
        return [
            7 => '7 Days',
            30 => '30 Days',
            60 => '60 Days',
            90 => '90 Days',
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'support-tickets-per-day';
    }
}
