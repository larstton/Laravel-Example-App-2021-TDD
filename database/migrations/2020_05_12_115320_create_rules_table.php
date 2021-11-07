<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRulesTable extends Migration
{
    public function up()
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id')->index();
            $table->enum('host_match_part', [
                'name', 'uuid', 'connect', 'tag', 'none',
            ]);
            $table->string('host_match_criteria');
            $table->unsignedTinyInteger('finish')->default(0);
            $table->enum('action', [
                'warn', 'alert', 'snooze', 'ignore',
            ]);
            $table->unsignedMediumInteger('position');
            $table->string('check_key', 150);
            $table->set('check_type', [
                'serviceCheck', 'webCheck', 'customCheck', 'cagent', 'snmpCheck',
            ]);
            $table->enum('function', [
                'last', 'sum', 'avg', 'min', 'max',
            ]);
            $table->enum('operator', [
                '<', '>', '<>', '=', 'empty', 'notEmpty',
            ]);
            $table->json('key_function')->nullable();
            $table->unsignedSmallInteger('results_range');
            $table->double('threshold', 20, 4);
            $table->uuid('user_id')->index();
            $table->unsignedTinyInteger('active');
            $table->string('expression_alias', 150)->nullable();
            $table->string('checksum', 32)->unique();
            $table->unsignedTinyInteger('mandatory');

            $table->timestamps();

            $table->index(['team_id', 'position']);
        });
    }
}
