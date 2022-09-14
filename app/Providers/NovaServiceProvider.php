<?php

namespace App\Providers;

use Ooredoo\DailyChecks\DailyChecks;
use App\Models\User;
use Laravel\Nova\Nova;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\UsersPerDay;
use App\Nova\Metrics\SupportTicketsPerDay;

use App\Nova\Metrics\SupportTicketByStatus;
use App\Nova\Metrics\SupportTicketsUnresolvedByUsers;

use App\Nova\Metrics\SupportTicketsStatusOpen;
use App\Nova\Metrics\SupportTicketsStatusClosed;
use App\Nova\Metrics\SupportTicketsStatusResolved;
use App\Nova\Metrics\SupportTicketsStatusInProgress;
use App\Nova\Metrics\SupportTicketsL2Escalation;
use App\Nova\Metrics\SupportTicketsL3Escalation;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Nova::style('admin-theme', 'css/admin.css');

        $this->app->alias(
            \App\Http\Controllers\Auth\LoginController::class,
            \Laravel\Nova\Http\Controllers\LoginController::class
        );
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                //->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            $usersx = User::select(['email'])->get();
            $allowed_users = [];
            foreach ($usersx as $userx) {
                $allowed_users[] = $userx->email;
            }
            return in_array($user->email, $allowed_users);
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            (new SupportTicketByStatus)->width('1/3'),
            (new SupportTicketsUnresolvedByUsers)->width('1/3'),
            (new SupportTicketsPerDay)->width('1/3'),
            (new SupportTicketsStatusOpen)->width('1/3'),
            (new SupportTicketsStatusInProgress)->width('1/3'),
            (new SupportTicketsStatusResolved)->width('1/3'),
            (new SupportTicketsStatusClosed)->width('1/3'),
            (new SupportTicketsL2Escalation)->width('1/3'),
            (new SupportTicketsL3Escalation)->width('1/3'),
            //(new NewUsers)->width('1/3'),
            //(new UsersPerDay)->width('1/3'),
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            new DailyChecks,
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
