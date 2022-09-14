<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class LoginDeviceType extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    public $name = 'Device Type';

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
        if($value=='Desktop'){
            return $query->where('is_desktop', 1);
        }

        if($value=='Tablet'){
            return $query->where('is_tablet', 1);
        }

        if($value=='Phone'){
            return $query->where('is_phone', 1);
        }

        return $query;
        
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
            'Desktop' => 'Desktop',
            'Tablet' => 'Tablet',
            'Phone' => 'Phone',
        ];
    }
}
