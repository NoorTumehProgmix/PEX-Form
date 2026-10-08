<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('institution', 191);
            $table->string('job_title', 191)->nullable();
            $table->string('email', 191)->unique();
            $table->string('phone', 20)->nullable();
            $table->string('part_type', 191);
            $table->string('sponsor_type', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_registrations');
    }
};
