<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostsTable extends Migration
{
    public function up()
    {
        Schema::create('hosts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('team_id')->index();
            $table->uuid('frontman_id')->index();
            $table->uuid('sub_unit_id')->index()->nullable();
            $table->text('description')->nullable();
            $table->json('tags_v2')->nullable();
            $table->string('state', 20)->default('PENDING');
            $table->uuid('user_id')->index();
            $table->uuid('last_update_user_id')->index()->nullable();
            $table->string('password', 12);
            $table->string('connect')->nullable();
            $table->boolean('active')->default(1);
            $table->boolean('cagent')->default(0)->nullable();
            $table->timestamp('cagent_last_updated_at')->nullable();
            $table->timestamp('snmp_check_last_updated_at')->nullable();
            $table->timestamp('web_check_last_updated_at')->nullable();
            $table->timestamp('service_check_last_updated_at')->nullable();
            $table->timestamp('custom_check_last_updated_at')->nullable();
            $table->json('inventory')->nullable();
            $table->unsignedInteger('cagent_metrics')->nullable()->default(0);
            $table->boolean('dashboard')->nullable()->default(1);
            $table->boolean('muted')->nullable()->default(0);
            $table->json('hw_inventory')->nullable();
            $table->enum('snmp_protocol', ['v2', 'v3'])->nullable();
            $table->unsignedInteger('snmp_port')->nullable();
            $table->string('snmp_community')->nullable();
            $table->unsignedInteger('snmp_timeout')->nullable();
            $table->enum('snmp_privacy_protocol', ['des', 'aes'])->nullable();
            $table->enum('snmp_security_level', ['authPriv', 'noAuthNoPriv', 'authNoPriv'])->nullable();
            $table->enum('snmp_authentication_protocol', ['sha', 'md5'])->nullable();
            $table->string('snmp_username')->nullable();
            $table->string('snmp_authentication_password')->nullable();
            $table->string('snmp_privacy_password')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['name', 'team_id']);
            $table->unique(['connect', 'team_id']);
        });
    }
}
