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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survivor_id');
            $table->unsignedInteger('req_water')->default(0);
            $table->unsignedInteger('req_food')->default(0);
            $table->unsignedInteger('req_medication')->default(0);
            $table->unsignedInteger('req_ammunition')->default(0);
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
        Schema::dropIfExists('trades');
    }
};
