<?php
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    public function up()
    {
        Schema::create(
            'posts',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title', 250);
                $table->string('subtitle', 250)->nullable();
                $table->string('thumbnail', 250)->nullable();
                $table->string('slug', 150)->nullable();
                $table->string('path')->nullable();
                $table->string('oldslug')->nullable();
                $table->string('description', 200)->nullable();
                $table->longText('content')->nullable();
                $table->text('text')->nullable();
                $table->string('status', 50)->index()->default('draft');
                $table->string('lang')->nullable();
                $table->string('meta_title')->nullable();
                $table->longText('meta_keywords')->nullable();
                $table->string('external_link')->nullable();
                $table->string('latlng')->nullable();
                $table->text('meta_description')->nullable();
                $table->bigInteger('views')->default(0);
                $table->integer('rel_id')->nullable();
                $table->integer('show_sitemap')->default(1);
                $table->integer('display_order')->default(100);
                $table->longText('images')->nullable();
                $table->longText('files')->nullable();
                $table->dateTime('date')->nullable();
                $table->dateTime('published_at')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->timestamps();
                $table->softDeletes();
            }
        );

        Schema::create(
            'post_metas',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('post_id')->index();
                $table->string('meta_key', 150)->index();
                $table->text('meta_value')->nullable();
                $table->unique(['post_id', 'meta_key']);

                $table->foreign('post_id')
                    ->references('id')
                    ->on('posts')
                    ->onDelete('cascade');
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('post_metas');
        Schema::dropIfExists('posts');
    }
}
