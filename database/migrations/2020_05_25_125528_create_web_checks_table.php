<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebChecksTable extends Migration
{
    public function up()
    {
        Schema::create('web_checks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('host_id')->index();
            $table->uuid('user_id')->index();
            $table->string('path')->default('/');
            $table->enum('protocol', ['https', 'http']);
            $table->unsignedInteger('port')->nullable();
            $table->string('expected_pattern', 75)->nullable();
            $table->enum('expected_pattern_presence', ['present', 'absent'])->default('present');
            $table->unsignedMediumInteger('expected_http_status')->nullable();
            $table->unsignedTinyInteger('search_html_source')->default(0);
            $table->float('time_out');
            $table->unsignedTinyInteger('ignore_ssl_errors')->default(0);
            $table->unsignedMediumInteger('check_interval')->default(90);
            $table->unsignedTinyInteger('dont_follow_redirects')->default(0);
            $table->enum('method', ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'])
                ->default('GET');
            $table->unsignedTinyInteger('active')->default(1)->index();
            $table->unsignedTinyInteger('in_progress')->default(0)->index();
            $table->unsignedTinyInteger('last_success')->nullable();
            $table->text('last_message')->nullable();
            $table->text('post_data')->nullable();
            $table->json('headers')->nullable();
            $table->string('headers_md5_sum', 32)->default(0);

            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->index(['host_id', 'path', 'method', 'port', 'headers_md5_sum']);
        });
    }
}
