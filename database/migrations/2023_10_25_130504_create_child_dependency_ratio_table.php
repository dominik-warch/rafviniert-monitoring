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
        Schema::create('child_dependency_ratio', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('value');
            $table->dateTime('date_of_dataset');
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
        Schema::dropIfExists('child_dependency_ratio');
    }
};
