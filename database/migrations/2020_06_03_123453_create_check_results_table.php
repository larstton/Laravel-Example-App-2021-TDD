<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckResultsTable extends Migration
{
    public function up()
    {
        Schema::create('check_results', function (Blueprint $table) {
            $table->id();
            $table->uuid('check_id')->index();
            $table->uuid('host_id')->index();
            $table->enum('check_type', [
                'serviceCheck',
                'webCheck',
                'customCheck',
                'cagent',
                'snmpCheck',
            ])->index();
            $table->json('data');
            $table->boolean('success')->nullable();
            $table->uuid('frontman_id')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('data_updated_at')->nullable()->index();
            $table->timestamp('created_at')->nullable();
        });
    }
}
