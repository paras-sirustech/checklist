<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCriticalChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('critical_checks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('critical_check_id')->nullable()->index();
            $table->integer('checklist_item_id')->nullable()->index();
            $table->string('checklist_item_name')->nullable();
            $table->enum('status', ['Okay', 'Not Okay'])->nullable()->index();
            $table->integer('support_ticket_id')->nullable()->index();
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
        Schema::dropIfExists('critical_checks');
    }
}
