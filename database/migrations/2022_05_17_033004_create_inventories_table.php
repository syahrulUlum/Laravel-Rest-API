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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survivor_id');
            $table->unsignedInteger('water');
            $table->unsignedInteger('food');
            $table->unsignedInteger('medication');
            $table->unsignedInteger('ammunition');
            $table->timestamps();

            $table->foreign('survivor_id')->references('id')->on('survivors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventories');
    }
};
