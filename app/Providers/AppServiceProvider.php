<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Observers\SupportTicketReplyObserver;
use App\Models\SupportTicketReply;

use App\Observers\SupportTicketObserver;
use App\Models\SupportTicket;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        SupportTicketReply::observe(SupportTicketReplyObserver::class);
        SupportTicket::observe(SupportTicketObserver::class);
    }
}
