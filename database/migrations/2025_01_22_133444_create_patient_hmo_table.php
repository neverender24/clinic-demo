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
        Schema::create('patient_hmo', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id');
            $table->integer('hmo_id');
            $table->date('date_registered')->nullable();
            $table->date('date_expiry')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_hmo');
    }
};
