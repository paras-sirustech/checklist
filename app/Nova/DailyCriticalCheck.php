<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BooleanGroup;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use App\Models\Shop;
use Laravel\Nova\Http\Requests\NovaRequest;

class DailyCriticalCheck extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\CriticalCaseP1';

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
        'id',
    ];

    public static function availableForNavigation(Request $request)
    {
        return $request->user()->access_level == "Admin";
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $shopChecklistOptions = [];
        if($this->shop){
            $shopChecklistOptions = $this->shop->getAttachedChecklist1Options();
        } else if ($request->viaResourceId!='') {
            $shop = Shop::find($request->viaResourceId);
            if($shop){
                $shopChecklistOptions = $shop->getAttachedChecklist1Options();
            }
        } else {
            $shopChecklistOptions = [];
        }
        return [
            //ID::make()->sortable(),
            BelongsTo::make('Shop')->searchable()->sortable(),
            Date::make('Date', 'checking_date')->sortable()->rules('required', 'date')->format('Do MMM YY')->detailLink(),
            BelongsTo::make('Filed By', 'submitter', 'App\Nova\User')->hideWhenCreating()->hideWhenUpdating(),
            BooleanGroup::make('Item Status', 'checklist_item_status')->options($shopChecklistOptions),
            DateTime::make('Filed', 'completed_at')->format('Do MMM YY, h:mm A')->sortable()->exceptOnForms(),
            HasMany::make('Support Tickets'),
        ];
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
