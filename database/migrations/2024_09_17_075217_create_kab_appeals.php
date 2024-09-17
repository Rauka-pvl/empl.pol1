<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kab_appeals', function (Blueprint $table) {
            $table->id();
            $table->integer('kab_id');
            $table->integer('type');
            $table->string('name');
            $table->string('text');
            $table->string('feedback');
            $table->string('file');
            $table->string('video');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kab_appeals');
    }
};
