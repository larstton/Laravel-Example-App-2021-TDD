<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateStatusPagesTable extends Migration
{
    public function up()
    {
        Schema::create('status_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id')->index();
            $table->string('token')->nullable();
            $table->string('title', 150)->nullable();
            $table->schemalessAttributes('meta');
            $table->binary('image')->nullable();
            $table->string('image_content_type')->nullable();
            $table->timestamps();
        });
    }
}
