<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupportTicket;

class SupportTicketEscalate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:escalate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Escalate Support Tickets as per defined SLA';

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
        $p1_escalations = SupportTicket::whereNotIn('status', ['Closed','Resolved'])->priorityChecklistItem('P1')->get();
        if ($p1_escalations && $p1_escalations->count()>0) {
            $this->info('Analysing ' . $p1_escalations->count() . ' Unresolved P1 Tickets');
        }
        foreach ($p1_escalations as $p1_escalation) {
            // check all eligible tickets
            if ($p1_escalation->isEligibileForEscalation('P1', 'L1')) {
                // Escalate L1
                $this->info($p1_escalation->escalate('P1', 'L1'));
            }

            if ($p1_escalation->isEligibileForEscalation('P1', 'L2')) {
                // Escalate L2
                $this->info($p1_escalation->escalate('P1', 'L2'));
            }

            if ($p1_escalation->isEligibileForEscalation('P1', 'L3')) {
                // Escalate L3
                $this->info($p1_escalation->escalate('P1', 'L3'));
            }
        }

        $p2_escalations = SupportTicket::whereNotIn('status', ['Closed','Resolved'])->priorityChecklistItem('P2')->get();
        if ($p2_escalations && $p2_escalations->count()>0) {
            $this->info('Analysing ' . $p2_escalations->count() . ' Unresolved P2 Tickets');
        }
        foreach ($p2_escalations as $p2_escalation) {
            // check all eligible tickets
            if ($p2_escalation->isEligibileForEscalation('P2', 'L1')) {
                // Escalate L1
                $this->info($p2_escalation->escalate('P2', 'L1'));
            }

            if ($p2_escalation->isEligibileForEscalation('P2', 'L2')) {
                // Escalate L2
                $this->info($p2_escalation->escalate('P2', 'L2'));
            }

            if ($p2_escalation->isEligibileForEscalation('P2', 'L3')) {
                // Escalate L3
                $this->info($p2_escalation->escalate('P2', 'L3'));
            }
        }
    }
}
