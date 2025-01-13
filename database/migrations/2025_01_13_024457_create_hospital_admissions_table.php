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
        Schema::create('hospital_admissions', function (Blueprint $table) {
            $table->id();
            $table->date('admission_date');
            $table->date('discharge_date')->nullable();
            $table->text('final_diagnosis')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('clinic_id');
            $table->integer('patient_id');
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_admissions');
    }
};
