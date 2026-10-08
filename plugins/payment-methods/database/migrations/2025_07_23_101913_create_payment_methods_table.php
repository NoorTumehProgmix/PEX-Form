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
            'payment_methods',
            function (Blueprint $table) {
                $table->id();
                $table->string('type', 50)->index();
                $table->string('name');
                $table->text('description')->nullable();
                $table->json('data')->nullable();
                $table->boolean('active')->default(1);
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
        Schema::dropIfExists('payment_methods');
    }
};
