<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Boolean;

use Laravel\Nova\Http\Requests\NovaRequest;

class UserLoginHistory extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\UserLoginHistory';
    public static $group = 'Logs';

    public static $displayInNavigation = true;
    public static $globallySearchable = false;

    public static function label()
    {
        return 'Login History';
    }

    public static $with = ['user'];
    

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'email';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'email','ip_address',
    ];

    public static $orderBy = ['created_at' => 'desc'];


    protected static function applyOrderings($query, array $orderings)
    {
        if (empty($orderings) && property_exists(static::class, 'orderBy')) {
            $orderings = static::$orderBy;
        }
        
        return parent::applyOrderings($query, $orderings);
    }



    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            //ID::make()->sortable()->detailLink(),

            DateTime::make('Login At', 'created_at')->format('MMM DD, h:mm A')->sortable()->onlyOnIndex(),
            DateTime::make('Login At', 'created_at')->format('Do MMM, YYYY \a\t h:mm A')->onlyOnDetail(),
            Badge::make('Type')->map([
                'Failed' => 'danger',
                'Successful' => 'success',
            ])->sortable(),
            Text::make('Email')->sortable()->exceptOnForms(),
            BelongsTo::make('User')->exceptOnForms(),
            Text::make('Level', 'user.access_level')->exceptOnForms(),
            
            Text::make('IP', 'ip_address', function () {
                return view('partials.user-login-history', [
                    'item' => $this
                ])->render();
            })->asHtml()->sortable()->exceptOnForms(),
            BelongsTo::make('Shop')->exceptOnForms(),

            Text::make('Device', 'device_name')->onlyOnDetail(),

            Text::make('OS', function () {
                return $this->os_name.' '.$this->os_version;
            })->onlyOnDetail(),
            Text::make('Browser', function () {
                return $this->browser_name.' '.round($this->browser_version);
            }),

            Boolean::make('Desktop', 'is_desktop')->onlyOnDetail(),
            Boolean::make('Mobile', 'is_mobile')->onlyOnDetail(),
            Boolean::make('Tablet', 'is_tablet')->onlyOnDetail(),

            Code::make('User Agent')->onlyOnDetail(),

            

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
        return [
            new Filters\LoginType,
            new Filters\LoginDateFrom,
            new Filters\LoginDateTo,
            new Filters\ShopFilter,
            new Filters\LoginDeviceType,
        ];
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
