<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\BelongsTo;

use Laravel\Nova\Http\Requests\NovaRequest;

class Shop extends Resource
{
    public static $group = 'Shops & Users';
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Shop';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            //ID::make()->sortable(),

            Text::make('Shop Name', 'name')
                ->sortable()
                ->rules('required', 'max:255')->detailLink(),

            BelongsTo::make('Assigned Support Staff', 'assignee', 'App\Nova\User')->searchable(),
            BelongsTo::make('Associated Checklist', 'checklist', 'App\Nova\Checklist'),
            BelongsTo::make('Cluster', 'cluster', 'App\Nova\Cluster'),
            Select::make('Type')->options([
                'Regular' => 'Regular',
                'Franchisee' => 'Franchisee',
            ])->sortable()->rules('required', 'max:255'),
            Number::make('Tickets', function () {
                return $this->actionable_tickets->count();
            })->onlyOnIndex(),
            HasMany::make('Daily Checks', 'daily_checks', 'App\Nova\DailyCheck'),
            HasMany::make('Support Tickets', 'support_tickets', 'App\Nova\SupportTicket'),
            DateTime::make('Created', 'created_at')->format('Do MMM YY, h:mm A')->sortable()->exceptOnForms(),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        if ($request->user()->access_level=='Cluster Manager') {
            return $query->whereIn('id', $request->user()->cluster_shop_ids());
        } elseif ($request->user()->access_level=='Support Staff') {
            return $query->where('assigned_to', $request->user()->id);
        }

        return $query;
    }

    public static function detailQuery(NovaRequest $request, $query)
    {
        if ($request->user()->access_level=='Cluster Manager') {
            return $query->whereIn('id', $request->user()->cluster_shop_ids());
        } elseif ($request->user()->access_level=='Support Staff') {
            return $query->where('assigned_to', $request->user()->id);
        }

        return $query;
    }



    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
