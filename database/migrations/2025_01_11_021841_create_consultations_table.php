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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('chief_complaint')->nullable();
            $table->text('test_results')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('management')->nullable();
            $table->integer('clinic_id')->unsigned();
            $table->integer('patient_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
