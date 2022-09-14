<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shop_id')->nullable()->index();
            $table->integer('assigned_to')->nullable()->index();
            $table->integer('created_by')->nullable()->index();
            $table->date('checking_date')->nullable()->index();
            $table->enum('status', ['Open','In Progress','Closed','Resolved'])->nullable()->index();
            $table->integer('daily_check_id')->nullable()->index();
            $table->integer('daily_check_item_id')->nullable()->index();
            $table->string('subject')->nullable();
            $table->longText('description')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_tickets');
    }
}
