<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use PhpParser\Node\Stmt\Label;

class Leave extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Leave';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

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

    public static function label()
    {
        return 'Out of Office';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        if ($request->user()->access_level == 'Admin') {
            $field = BelongsTo::make('User', 'user', 'App\Nova\User')->searchable() ;
        } else {
            $field = Text::make('', 'user_id')->withMeta(['type' => 'hidden', 'value' => $request->user()->id])->onlyOnForms();
        }
        return [
            ID::make()->sortable(),
            $field,
            Date::make('From Date')->format('Do MMM YY')->sortable(),
            Date::make('To Date')->format('Do MMM YY')->sortable()->rules('after_or_equal:from_date'),
            Textarea::make('Note')->rules('required')->rows(5)->alwaysShow()->hideFromIndex(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

}
