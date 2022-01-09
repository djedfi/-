<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Modelos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('modelos',function (Blueprint $table)
        {
            $table->id();
            $table->unsignedBigInteger('make_id');
            $table->string('name',45);
            $table->timestamps();

             //llaves foraneas
             $table->foreign('make_id')->references('id')->on('makes')->onUpdate('cascade')->onDelete('restrict');
        });

        if(Schema::hasTable('trims'))
        {
            Schema::table('trims', function (Blueprint $table) {
                $table->foreign('modelo_id')->references('id')->on('modelos')->onUpdate('cascade')->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('modelos');
    }
}
