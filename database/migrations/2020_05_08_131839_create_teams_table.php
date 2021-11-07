<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->uuid('id')->index();

            $table->string('name')->nullable();
            $table->unsignedMediumInteger('max_hosts')->default(5);
            $table->unsignedSmallInteger('max_members')->default(10);
            $table->unsignedMediumInteger('max_recipients')->default(10);

            $table->uuid('default_frontman_id')->nullable();
            $table->unsignedMediumInteger('max_frontmen');
            $table->unsignedMediumInteger('min_check_interval')->default(90);
            $table->unsignedMediumInteger('data_retention')->nullable();
            $table->string('plan', 200)->nullable();
            $table->string('timezone', 30)->default('Etc/GMT');
            $table->string('currency', 15)->nullable();
            $table->json('registration_track')->nullable();
            $table->string('partner', 80)->nullable();
            $table->json('partner_extra_data')->nullable();
            $table->string('date_format', 2)->nullable()->default('L.');
            $table->unsignedTinyInteger('has_granted_access_to_support')->nullable()->default(0);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('upgraded_at')->nullable();
            $table->softDeletes();

            $table->timestamps();
        });
    }
}
