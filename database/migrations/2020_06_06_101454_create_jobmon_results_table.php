<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobmonResultsTable extends Migration
{
    public function up()
    {
        Schema::create('jobmon_results', function (Blueprint $table) {
            $table->id();
            $table->uuid('host_id')->index();
            $table->string('job_id', 100);
            $table->json('data');
            $table->timestamp('next_run')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }
}
