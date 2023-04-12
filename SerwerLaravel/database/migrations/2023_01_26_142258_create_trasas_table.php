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
        Schema::create('trasas', function (Blueprint $table) {
            $table->id();
            $table->string('nazwa');
            $table->string('tworca');
            $table->integer('dodany_znajomy')->default(0);
            $table->integer('czy_zakonczona');
            $table->double('przewidywana_dlugosc', 8, 2);
            $table->double('przewidywany_czas', 8, 2);
            $table->integer('id_naSerwerze')->nullable();
            $table->integer('Id_Czatu')->nullable();
            $table->integer('id_tworcyOnSerwer')->nullable();
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
        Schema::dropIfExists('trasas');
    }
};
