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
        Schema::create('cinemas', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->foreignUuid('logo_id')->nullable()->constrained('file_sources')->nullOnDelete();
            $table->string('address');
            $table->string('reference_point')->nullable();
            $table->decimal('longitude', 9, 6);
            $table->decimal('latitude', 9, 6);
            $table->string('phone')->unique();
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
        Schema::dropIfExists('cinemas');
    }
};
