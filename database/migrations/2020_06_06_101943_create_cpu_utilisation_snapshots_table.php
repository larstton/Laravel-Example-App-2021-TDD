<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpuUtilisationSnapshotsTable extends Migration
{
    public function up()
    {
        Schema::create('cpu_utilisation_snapshots', function (Blueprint $table) {
            $table->id();
            $table->uuid('host_id')->index();
            $table->json('settings');
            $table->json('top');
            $table->timestamp('created_at')->nullable();
        });
    }
}
