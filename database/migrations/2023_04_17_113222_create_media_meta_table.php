<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_metas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('caption')->nullable();
            $table->string('text')->nullable();
            $table->string('description')->nullable();
            $table->string('lang')->nullable();
            $table->unsignedBigInteger('media_files_id'); // foreign key column
            $table->foreign('media_files_id')->references('id')->on('media_files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_metas');
    }
};
