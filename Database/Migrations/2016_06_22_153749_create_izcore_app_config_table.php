<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIzcoreAppConfigTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(
            'izcore_app_config',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('value');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('izcore_app_config');
    }

}
