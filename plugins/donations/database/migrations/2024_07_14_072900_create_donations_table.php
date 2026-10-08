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
            'donations',
            function (Blueprint $table) {
                $table->id();
                $table->string('name', 255)->nullable()->default(null);
                $table->string('email', 255)->nullable()->default(null);
                $table->string('phone', 255)->nullable()->default(null);
                $table->string('payment_method', 255)->nullable()->default(null);
                $table->string('payment_status', 255)->nullable()->default(null);
                $table->string('payment_id', 255)->nullable()->default(null);
                $table->string('payment_amount', 255)->nullable()->default(null);
                $table->string('payment_currency', 255)->nullable()->default(null);
                $table->string('recurring', 255)->nullable()->default(null);
                $table->string('recurring_amount', 255)->nullable()->default(null);
                $table->string('recurring_interval', 255)->nullable()->default(null);
                $table->longText('note')->nullable()->default(null);
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
        Schema::dropIfExists('donations');
    }
};
