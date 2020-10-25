<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->enum('status', ['scheduled', 'notifying', 'sent', 'error']);
            $table->dateTime('launch_time');
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}
