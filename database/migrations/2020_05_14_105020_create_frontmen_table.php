<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrontmenTable extends Migration
{
    public function up()
    {
        Schema::create('frontmen', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id')->index();
            $table->string('location', 50)->nullable();
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->string('password', 18);
            $table->uuid('user_id')->index();
            $table->json('host_info')->nullable();
            $table->timestamp('host_info_last_updated_at')->nullable();
            $table->string('version', 100)->nullable();
            $table->timestamps();
        });
    }
}
