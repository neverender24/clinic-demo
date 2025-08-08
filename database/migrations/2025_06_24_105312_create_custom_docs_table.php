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
        Schema::create('custom_docs', function (Blueprint $table) {
            $table->id();
            $table->string('doc_name');
            $table->integer('consultation_id')->length(50);
            $table->longText('content')->nullable();
            $table->string('size', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_docs');
    }
};
