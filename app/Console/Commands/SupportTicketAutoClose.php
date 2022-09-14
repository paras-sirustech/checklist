<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupportTicket;

class SupportTicketAutoClose extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:autoclose';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-close Resolved Support Tickets after 3 days';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tickets = SupportTicket::where('status', 'Resolved')->whereDate('resolved_date', '<', now()->subDays(3)->toDateString())->get();

        foreach ($tickets as $ticket) {
            $ticket->status = 'Closed';
            $ticket->save();
            $this->info('Ticket auto closed: ' . $ticket->id);
        }
    }
}
