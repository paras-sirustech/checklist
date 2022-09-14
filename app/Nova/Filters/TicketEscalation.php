<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class TicketEscalation extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    public $name = 'Escalation';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        if ($value=='No Escalation') {
            return $query->whereNull('l2_escalation_at')->whereNull('l3_escalation_at');
        }

        if ($value=='L2 Escalation') {
            return $query->whereNotNull('l2_escalation_at');
        }

        if ($value=='L3 Escalation') {
            return $query->whereNotNull('l3_escalation_at');
        }
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            'No Escalation' => 'No Escalation',
            'L2 Escalation' => 'L2 Escalation',
            'L3 Escalation' => 'L3 Escalation',
        ];
    }
}
