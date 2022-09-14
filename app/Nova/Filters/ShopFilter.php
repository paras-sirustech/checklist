<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use App\Models\Shop;

class ShopFilter extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    public $name = 'Shop';

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
        if($value=='-'){
            return $query->whereNull('shop_id');
        }
        return $query->where('shop_id', $value);
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
        if ($request->user()->access_level=='Cluster Manager') {
            $shops = Shop::whereIn('id', $request->user()->cluster_shop_ids())->get();
        } else {
            $shops = Shop::get();
        }

        foreach ($shops as $shop) {
            $filter[$shop->name] = $shop->id;
        }
        $filter['Not Linked to any Shop'] = '-';
        return $filter;
    }
}
