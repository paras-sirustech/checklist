<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\File;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Http\Requests\NovaRequest;
use Gkermer\TextAutoComplete\TextAutoComplete;
use App\Models\ChecklistItem;

class SupportTicket extends Resource
{
    public static $group = 'Support';
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\SupportTicket';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'subject';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'subject', 'status'
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
            ID::make('Ticket ID', 'id')->detailLink(),

            TextAutoComplete::make('Subject', 'subject')->items(ChecklistItem::where('checklist_id', 1)->where('priority', 'P1')->get()->pluck('name'))->sortable()
            ->rules('required', 'max:255'),

            BelongsTo::make('Shop')->nullable(),

            Select::make('Status')->options([
                'Open' => 'Open',
                'In Progress' => 'In Progress',
                'Closed' => 'Closed',
                'Resolved' => 'Resolved',
            ])->exceptOnForms(),

            BelongsTo::make('Assigned To', 'assignee', 'App\Nova\User')->sortable(),

            Text::make('Priority')->sortable()->exceptOnForms()->hideFromIndex(),

            Date::make('Due Date')->format('Do MMM YY')->sortable()->exceptOnForms(),

            Date::make('Date Resolved', 'resolved_date')->format('Do MMM YY')->onlyOnDetail(),

            BelongsTo::make('Created By', 'creator', 'App\Nova\User')->onlyOnDetail(),
            DateTime::make('Created', 'created_at')->format('Do MMM YY')->sortable()->exceptOnForms(),
            DateTime::make('Updated', 'updated_at')->format('Do MMM YY')->sortable()->exceptOnForms(),
            Textarea::make('Description')->rules('required', 'min:10')->rows(5)->alwaysShow()->hideFromIndex(),
            Image::make('Attachment')->disk('public')->storeAs(function (Request $request) {
                return Str::slug($request->subject, '-') . '-attachment-' . time() . '.' . $request->attachment->getClientOriginalExtension();
            })->hideFromIndex(),
            HasMany::make('Replies', 'replies', 'App\Nova\SupportTicketReply'),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        if ($request->user()->access_level=='Support Staff') {
            return $query->where('assigned_to', $request->user()->id);
        } elseif ($request->user()->access_level=='Shop Staff') {
            return $query->where('created_by', $request->user()->id);
        } elseif ($request->user()->access_level=='Cluster Manager') {
            return $query->whereIn('shop_id', $request->user()->cluster_shop_ids());
        }

        return $query;
    }

    public static function detailQuery(NovaRequest $request, $query)
    {
        if ($request->user()->access_level=='Support Staff') {
            return $query->where('assigned_to', $request->user()->id);
        } elseif ($request->user()->access_level=='Shop Staff') {
            return $query->where('created_by', $request->user()->id);
        } elseif ($request->user()->access_level=='Cluster Manager') {
            return $query->whereIn('shop_id', $request->user()->cluster_shop_ids());
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
        return [
            new Filters\TicketStatus,
            new Filters\TicketEscalation,
            new Filters\TicketCreatedFrom,
            new Filters\TicketCreatedTo,
            new Filters\ShopFilter,
            new Filters\TicketAssignee,
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
        return [
            /* (new Actions\TicketChangePriority)->canSee(function ($request) {
                if ($request->user()->access_level=='Admin') {
                    return true;
                }
                return false;
            })->canRun(function ($request, $user) {
                if ($request->user()->access_level=='Admin') {
                    return true;
                }
                return false;
            }), */
            (new Actions\TicketSetDueDate)->canSee(function ($request) {
                if ($request->user()->access_level=='Admin' || $request->user()->access_level=='Support Staff') {
                    return true;
                }
                return false;
            })->canRun(function ($request, $user) {
                if ($request->user()->access_level=='Admin' || $request->user()->access_level=='Support Staff') {
                    return true;
                }
                return false;
            }),
            (new Actions\TicketChangeAssignee)->canSee(function ($request) {
                if ($request->user()->access_level=='Admin' || $request->user()->access_level=='Support Staff') {
                    return true;
                }
                return false;
            })->canRun(function ($request, $user) {
                if ($request->user()->access_level=='Admin' || $request->user()->access_level=='Support Staff') {
                    return true;
                }
                return false;
            }),
            (new Actions\TicketChangeStatus)->canSee(function ($request) {
                return true;
            })->canRun(function ($request, $user) {
                return true;
            }),
        ];
    }
}
