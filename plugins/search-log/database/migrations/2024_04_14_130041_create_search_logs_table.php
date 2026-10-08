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
        Schema::create(
            'search_logs',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->longText('text')->nullable();
                $table->string('lang')->nullable();
                $table->string('ip_address')->nullable();
                $table->json('data')->nullable();
                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists('search_logs');
    }
};
