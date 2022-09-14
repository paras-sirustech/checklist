<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\BelongsToMany;

class User extends Resource
{
    public static $group = 'Shops & Users';
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\User';

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
        'name', 'email',
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

            //Gravatar::make(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255')->detailLink(),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            Select::make('Access Level', 'access_level')->options([
                'Shop Staff' => 'Shop Staff',
                'Support Staff' => 'Support Staff',
                'Cluster Manager' => 'Cluster Manager',
                'Admin' => 'Admin',
            ])->sortable(),

            BelongsToMany::make('Shops')
            ->fields(function () {
                return [
                    Select::make('Type')->options([
                        'Branch Manager' => 'Branch Manager',
                        'Branch Admin' => 'Branch Admin',
                        'Supervisor' => 'Supervisor',
                    ])->rules('required'),
                ];
            }),

            HasMany::make('User Login History', 'login_history', 'App\Nova\UserLoginHistory'),

            //HasMany::make('Daily Checks', 'daily_checks', 'App\Nova\DailyCheck'),
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
            new Filters\UserAccessLevel,
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
