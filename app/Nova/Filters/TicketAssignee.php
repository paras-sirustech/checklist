<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use App\Models\User;

class TicketAssignee extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    public $name = 'Assigned To';

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
        if ($value=='-') {
            return $query->whereNull('assigned_to');
        }
        return $query->where('assigned_to', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        $filter = [];
        $users = User::whereIn('access_level', ['Support Staff','Admin'])->get();

        foreach ($users as $user) {
            $filter[$user->name] = $user->id;
        }
        $filter['Not Assigned to anyone'] = '-';
        return $filter;
    }
}
