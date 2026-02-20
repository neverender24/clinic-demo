<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_medicine', function (Blueprint $table) {
            $table->unsignedTinyInteger('batch')->default(1)->after('sort');
        });
    }

    public function down(): void
    {
        Schema::table('consultation_medicine', function (Blueprint $table) {
            $table->dropColumn('batch');
        });
    }
};
