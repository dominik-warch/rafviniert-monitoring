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
        Schema::create('qualifying_residents_gender', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('value_gender_1');
            $table->float('value_gender_2');
            $table->float('value_gender_3');
            $table->dateTime('date_of_dataset');
            $table->string('reference_geometry');
            $table->magellanMultiPolygon('geometry');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qualifying_residents_gender');
    }
};
