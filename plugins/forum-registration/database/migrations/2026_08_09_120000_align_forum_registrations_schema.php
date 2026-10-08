<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_registrations', function (Blueprint $table) {
            $table->string('name', 191)->nullable(false)->change();
            $table->string('institution', 191)->nullable(false)->change();
            $table->string('job_title', 191)->nullable()->change();
            $table->string('email', 191)->nullable(false)->change();
            $table->string('phone', 20)->nullable()->change();
            $table->string('part_type', 191)->nullable(false)->change();
            $table->string('sponsor_type', 191)->nullable()->change();
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('forum_registrations', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->string('name', 255)->nullable()->change();
            $table->string('institution', 255)->nullable()->change();
            $table->string('job_title', 255)->nullable()->change();
            $table->string('email', 255)->nullable()->change();
            $table->string('phone', 255)->nullable()->change();
            $table->string('part_type', 255)->nullable()->change();
            $table->string('sponsor_type', 255)->nullable()->change();
        });
    }
};
