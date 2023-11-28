<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('citizens_master', function (Blueprint $table) {
            $table->id();
            $table->string('gender');
            $table->integer('year_of_birth');
            $table->date('dataset_date');
            $table->string('zip_code');
            $table->string('city');
            $table->string('street');
            $table->string('housenumber');
            $table->string('housenumber_ext')->nullable();
            $table->magellanPoint('geometry', 4326);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citizens_master');
    }
};
