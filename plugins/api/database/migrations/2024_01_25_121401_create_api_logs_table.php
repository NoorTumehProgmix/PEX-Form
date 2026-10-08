<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(
            'awap_api_logs',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('api_id')->constrained('awap_apis');
                $table->integer('attempt_id');
                $table->json('request')->nullable();
                $table->json('response')->nullable();
                $table->enum('type', ['client/edge', 'edge/origin'])->nullable();
                $table->string('ip')->nullable();
                $table->unsignedSmallInteger('status_code')->nullable();
                $table->dateTime('start')->nullable();
                $table->dateTime('end')->nullable();
                $table->unsignedInteger('duration')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('awap_api_logs');
    }
};
