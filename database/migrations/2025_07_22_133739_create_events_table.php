<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained('users');
            $table->string('title');
            $table->text('description');
            $table->string('venue');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->enum('status', ['draft', 'published']);
            $table->timestamps();        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};
