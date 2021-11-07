<?php

use App\Enums\TeamMemberRole;
use App\Enums\TeamStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default(TeamMemberRole::Admin);
            $table->string('team_status')->default(TeamStatus::Joined);
            $table->boolean('terms_accepted');
            $table->boolean('privacy_accepted');
            $table->boolean('product_news')->default(true);

            $table->string('nickname')->nullable();
            $table->string('host_tag')->nullable();

            $table->string('lang')->nullable()->default('en');

            $table->uuid('team_id')->index();
            $table->uuid('sub_unit_id')->nullable()->index();

            $table->text('notes')->nullable();
            $table->rememberToken();

            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }
}
