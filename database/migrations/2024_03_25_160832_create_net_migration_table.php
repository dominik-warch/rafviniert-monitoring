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
        Schema::create('net_migration', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('value');
            $table->int('year');
            $table->string('reference_geometry');
            $table->magellanMultiPolygon('geometry', 4326);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('net_migration');
    }
};
