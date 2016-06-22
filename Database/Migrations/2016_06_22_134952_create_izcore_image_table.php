<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIzcoreImageTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(
            'izcore_image',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('path');
                $table->string('type');
                $table->string('is_active');
                $table->integer('imageable_id');
                $table->string('imageable_type');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('izcore_image');
    }

}
