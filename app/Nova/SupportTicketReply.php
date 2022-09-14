<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\File;
use Illuminate\Support\Str;

class SupportTicketReply extends Resource
{
    public static $globallySearchable = false;
    public static $displayInNavigation = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\SupportTicketReply';

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

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            BelongsTo::make('Ticket', 'ticket', 'App\Nova\SupportTicket')->onlyOnDetail(),

            Text::make('Summary', 'description')->displayUsing(function ($value) {
                return Str::limit($value, '100', '...');
            })->asHtml()->onlyOnIndex()->detailLink(),

            
            DateTime::make('On', 'created_at')->format('Do MMM YY, h:mm A')->sortable()->exceptOnForms(),

            BelongsTo::make('By', 'user', 'App\Nova\User')->exceptOnForms(),

            Textarea::make('Description')->rows(10)->alwaysShow()->hideFromIndex(),

            File::make('Attachment')->disk('local')->storeAs(function (Request $request) {
                return 'reply-attachment-' . time() . '.' . $request->attachment->getClientOriginalExtension();
            }),
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
