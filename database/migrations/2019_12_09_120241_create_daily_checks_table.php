<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_checks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shop_id')->nullable()->index();
            $table->date('checking_date')->nullable()->index();
            $table->integer('submitted_by')->nullable()->index();
            $table->integer('checklist_id')->nullable()->index();
            $table->tinyInteger('is_submission_complete')->nullable()->default(0)->index();
            $table->timestamp('completed_at')->nullable();
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
        Schema::dropIfExists('daily_checks');
    }
}
