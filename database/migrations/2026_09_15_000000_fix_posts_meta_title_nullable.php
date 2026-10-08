<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fixes a regression from 2024_07_01_094804_update_posts_table_lengths.php:
     * that migration called ->change() on `meta_title` without repeating
     * ->nullable(), which (per Laravel/Doctrine DBAL behavior) silently reset
     * the column back to NOT NULL with no default — breaking every insert
     * (e.g. creating a new Page) that doesn't explicitly supply meta_title.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('meta_title', 500)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
