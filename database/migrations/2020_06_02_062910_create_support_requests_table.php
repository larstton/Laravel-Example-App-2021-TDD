<?php

use App\Enums\SupportRequestState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('support_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_id')->index();
            $table->string('email')->index();
            $table->string('subject');
            $table->longText('body');
            $table->enum('state', ['open', 'in_progress', 'closed'])->default(SupportRequestState::Open);
            $table->json('attachment')->nullable();
            $table->timestamps();
        });
    }
}
