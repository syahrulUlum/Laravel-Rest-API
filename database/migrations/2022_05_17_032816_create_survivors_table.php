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
        Schema::create('survivors', function (Blueprint $table) {
            $table->id();
            $table->string("name", 200);
            $table->unsignedInteger('age');
            $table->enum('gender', ['F', 'M']);
            $table->string('last_location', 25);
            $table->unsignedInteger('reported')->default(0);
            $table->boolean('is_infected')->default(0);
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
        Schema::dropIfExists('survivors');
    }
};
