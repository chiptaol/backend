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
            $table->json('countries')->nullable();
            $table->text('trailer_path')->nullable();
            $table->text('poster_path')->nullable();
            $table->text('backdrop_path')->nullable();
            $table->string('age_rating')->nullable();
            $table->float('rating')->nullable();
            $table->date('release_date')->nullable();
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
