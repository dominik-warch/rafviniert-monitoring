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
        Schema::create('citizen_transaction', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type');
            $table->date('transaction_date');
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
        Schema::dropIfExists('citizen_transaction');
    }
};
