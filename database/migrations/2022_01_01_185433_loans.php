<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Loans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('car_id');
            $table->decimal('price',10,2);
            $table->decimal('downpayment',10,2);
            $table->decimal('value_trade',10,2)->nullable();
            $table->integer('long_term');
            $table->decimal('interest_rate',10,2);
            $table->decimal('payments',10,2);
            $table->date('start_day');
            $table->integer('tipo_loan');
            $table->timestamps();
        });
        //1=24 meses; 2=36 meses; 3=48 meses; 4=60 meses; 6=72 meses; 7=84 meses;
        DB::statement('ALTER TABLE loans ADD CONSTRAINT chk_long_term CHECK (long_term between 1 and 6);');

        //1=buy; 2= trade; 3= finance
        DB::statement('ALTER TABLE loans ADD CONSTRAINT chk_tipo_loan CHECK (tipo_loan between 1 and 3);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('loans');
    }
}
