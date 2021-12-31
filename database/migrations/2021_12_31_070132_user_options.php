<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserOptions extends Migration
{
    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user_optios',function(Blueprint $table)
        {
            //relaciones
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                            ->references('id')
                            ->on('users')
                            ->onUpdate('cascade')
                            ->onDelete('restrict');
            $table->unsignedBigInteger('option_id');
            $table->foreign('option_id')
                            ->references('id')
                            ->on('options_app')
                            ->onUpdate('cascade')
                            ->onDelete('restrict');
            $table->primary(['user_id', 'option_id']);
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
        //
        Schema::dropIfExists('user_optios');
    }
}
