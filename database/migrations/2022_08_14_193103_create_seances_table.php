<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cinema_id')->constrained('cinemas')->cascadeOnDelete();
            $table->foreignId('hall_id')->constrained('halls')->cascadeOnDelete();
            $table->foreignId('premiere_id')->constrained('premieres')->cascadeOnDelete();
            $table->foreignId('format_id')->nullable()->constrained('formats')->nullOnDelete();
            $table->json('prices')->nullable();
            $table->date('start_date');
            $table->timestamp('start_date_time')->nullable();
            $table->timestamp('end_date_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seances');
    }
};
