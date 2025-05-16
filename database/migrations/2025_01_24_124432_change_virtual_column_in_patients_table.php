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
        Schema::table('patients', function (Blueprint $table) {
            $table->string('full_name', 135)
                ->virtualAs('CONCAT(last_name, ", ", first_name, " ", IF(middle_name IS NULL, "", concat( LEFT(middle_name, 1), ".")))')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('full_name', 135)->virtualAs('CONCAT(last_name, ", ", first_name, " ", LEFT(middle_name, 1), IF(middle_name IS NULL, "", "."))')->change();
        });
    }
};
