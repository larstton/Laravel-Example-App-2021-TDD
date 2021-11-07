<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubUnitsTable extends Migration
{
    public function up()
    {
        Schema::create('sub_units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id')->index();
            $table->string('short_id', 20);
            $table->string('name', 150)->nullable();
            $table->text('information')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'short_id']);
        });
    }
}
