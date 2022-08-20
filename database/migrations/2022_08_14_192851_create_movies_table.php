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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tmdb_id')->unique();
            $table->string('title');
            $table->string('original_title');
            $table->text('description')->nullable();
            $table->json('genres')->nullable();
            $table->integer('duration')->nullable();
            $table->text('tagline')->nullable();
            $table->json('actors')->nullable();
            $table->json('directors')->nullable();
            $table->text('trailer')->nullable();
            $table->text('poster')->nullable();
            $table->smallInteger('age_rating');
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
        Schema::dropIfExists('movies');
    }
};
