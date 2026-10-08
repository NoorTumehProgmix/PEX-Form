<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table(
            'states',
            function (Blueprint $table) {
                $table->string('iso_code')->after('name')->nullable();
                $table->boolean('active')->default(true)->after('name');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table(
            'states',
            function (Blueprint $table) {
                $table->dropColumn(['iso_code', 'active']);
            }
        );
    }
};
