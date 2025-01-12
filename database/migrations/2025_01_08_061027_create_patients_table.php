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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('last_name', 45);
            $table->string('first_name', 45);
            $table->string('middle_name', 45)->nullable();
            $table->string('full_name', 135)->virtualAs('CONCAT(last_name, ", ", first_name, " ", LEFT(middle_name, 1), IF(middle_name IS NULL, "", "."))');
            $table->date('birthday');
            $table->string('sex', 1);
            $table->string('contact_details', 255)->nullable();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
