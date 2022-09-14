<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\DailyCheck' => 'App\Policies\DailyCheckPolicy',
        'App\Models\SupportTicket' => 'App\Policies\SupportTicketPolicy',
        'App\Models\Checklist' => 'App\Policies\ChecklistPolicy',
        'App\Models\ShopIpAddress' => 'App\Policies\ShopIpAddressPolicy',
        'App\Models\Shop' => 'App\Policies\ShopPolicy',
        'App\Models\User' => 'App\Policies\UserPolicy',
        'App\Models\Cluster' => 'App\Policies\ClusterPolicy',
        'App\Models\UserLoginHistory' => 'App\Policies\UserLoginHistoryPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
