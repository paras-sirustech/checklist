<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlaColumnToSupportTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->integer('checklist_item_id')->index()->nullable();
            $table->timestamp('last_support_team_ticket_update')->index()->nullable();
            $table->timestamp('l1_escalation_at')->index()->nullable();
            $table->timestamp('l2_escalation_at')->index()->nullable();
            $table->timestamp('l3_escalation_at')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn('checklist_item_id');
            $table->dropColumn('last_support_team_ticket_update');
            $table->dropColumn('l1_escalation_at');
            $table->dropColumn('l2_escalation_at');
            $table->dropColumn('l3_escalation_at');
        });
    }
}
