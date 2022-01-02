<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Configs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->integer('long_term_default');
            $table->decimal('porc_downpay_default',10,2);
            $table->decimal('int_rate_default',10,2);
            $table->decimal('latefee_default',10,2);
            $table->foreign('branch_id')->references('id')->on('branches')->onUpdate('cascade')->onDelete('restrict');

            $table->timestamps();
        });
        //1=24 meses; 2=36 meses; 3=48 meses; 4=60 meses; 6=72 meses; 7=84 meses;
        DB::statement('ALTER TABLE configs ADD CONSTRAINT chk_long_term_default CHECK (long_term_default between 1 and 6);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
