<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLoginHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_login_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['Successful','Failed'])->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('ip_address')->nullable();
            $table->unsignedInteger('shop_id')->nullable()->index();
            $table->string('user_agent')->nullable();
            $table->tinyInteger('is_desktop')->nullable()->default(0);
            $table->tinyInteger('is_phone')->nullable()->default(0);
            $table->tinyInteger('is_tablet')->nullable()->default(0);
            $table->tinyInteger('is_bot')->nullable()->default(0);
            $table->string('os_name')->nullable();
            $table->string('os_version')->nullable();
            $table->string('device_name')->nullable();
            $table->string('browser_name')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('bot_name')->nullable();
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
        Schema::dropIfExists('user_login_histories');
    }
}
