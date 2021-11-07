<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustSendtoLengthForRecipient extends Migration
{
    public function up()
    {
        Schema::table('recipients', function (Blueprint $table) {
            $table->string('sendto', 1024)->change()->after('media_type');
        });
    }
}
