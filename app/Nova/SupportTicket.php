<?php

namespace App\Nova;

use App\Nova\Filters\TicketById;
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
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

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
        'id', 'subject', 'status'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $unAvailable = \App\Models\Leave::whereRaw('? BETWEEN from_date AND to_date', date('Y-m-d'))->pluck('user_id');
        return [
            ID::make('Ticket ID', 'id')->detailLink(),

            TextAutoComplete::make('Subject', 'subject')->items(ChecklistItem::where('checklist_id', 1)->where('priority', 'P1')->get()->pluck('name'))->sortable()
                ->rules('required', 'unique:support_tickets,subject,NULL,id,shop_id,' . $request->shop, 'max:255'),

            BelongsTo::make('Shop')->nullable(),

            Select::make('Status')->options([
                'Open' => 'Open',
                'In Progress' => 'In Progress',
                'Closed' => 'Closed',
                'Resolved' => 'Resolved',
            ])->exceptOnForms(),

//            BelongsTo::make('Assigned To', 'availableUser', 'App\Nova\User')->sortable(),

            Select::make('Assigned To')->options(
                \App\Models\User::whereNotIn('id', $unAvailable)->pluck('name', 'id'))->sortable()
                ->rules('required')->displayUsingLabels(),

            Text::make('Priority')->sortable()->exceptOnForms()->hideFromIndex(),

            Date::make('Due Date')->format('Do MMM YY')->sortable()->exceptOnForms(),

            Date::make('Date Resolved', 'resolved_date')->format('Do MMM YY')->onlyOnDetail(),

            BelongsTo::make('Created By', 'creator', 'App\Nova\User')->onlyOnDetail(),
            DateTime::make('Created', 'created_at')->format('Do MMM YY')->sortable()->exceptOnForms(),
            DateTime::make('Updated', 'updated_at')->format('Do MMM YY')->sortable()->exceptOnForms(),
            Textarea::make('Description')->rules('required')->rows(5)->alwaysShow()->hideFromIndex(),
            Image::make('Attachment')->disk('public')->storeAs(function (Request $request) {
                return Str::slug($request->subject, '-') . '-attachment-' . time() . '.' . $request->attachment->getClientOriginalExtension();
            })->hideFromIndex(),
            HasMany::make('Replies', 'replies', 'App\Nova\SupportTicketReply'),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        if ($request->user()->access_level == 'Support Staff') {
            return $query->where('assigned_to', $request->user()->id);
        } elseif ($request->user()->access_level == 'Shop Staff') {
            return $query->where('created_by', $request->user()->id);
        } elseif ($request->user()->access_level == 'Cluster Manager') {
            return $query->whereIn('shop_id', $request->user()->cluster_shop_ids());
        }

        return $query;
    }

    public static function detailQuery(NovaRequest $request, $query)
    {
        if ($request->user()->access_level == 'Support Staff') {
            return $query->where('assigned_to', $request->user()->id);
        } elseif ($request->user()->access_level == 'Shop Staff') {
            return $query->where('created_by', $request->user()->id);
        } elseif ($request->user()->access_level == 'Cluster Manager') {
            return $query->whereIn('shop_id', $request->user()->cluster_shop_ids());
        }

        return $query;
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
        return [
            new Filters\TicketStatus,
            new Filters\TicketEscalation,
            new Filters\TicketCreatedFrom,
            new Filters\TicketCreatedTo,
            new Filters\ShopFilter,
            new Filters\TicketAssignee,
            new Filters\TicketCreatedBy
        ];
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
            new DownloadExcel,
            (new Actions\TicketSetDueDate)->canSee(function ($request) {
                if ($request->user()->access_level == 'Admin' || $request->user()->access_level == 'Support Staff') {
                    return true;
                }
                return false;
            })->canRun(function ($request, $user) {
                if ($request->user()->access_level == 'Admin' || $request->user()->access_level == 'Support Staff') {
                    return true;
                }
                return false;
            }),
            (new Actions\TicketChangeAssignee)->canSee(function ($request) {
                if ($request->user()->access_level == 'Admin' || $request->user()->access_level == 'Support Staff') {
                    return true;
                }
                return false;
            })->canRun(function ($request, $user) {
                if ($request->user()->access_level == 'Admin' || $request->user()->access_level == 'Support Staff') {
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
