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
            $table->unsignedBigInteger('car_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('price',10,2);
            $table->decimal('downpayment',10,2);
            $table->decimal('value_trade',10,2)->nullable();
            $table->integer('long_term');
            $table->decimal('interest_rate',10,2)->nullable();
            $table->decimal('taxes_rate',10,2)->nullable();
            $table->decimal('minimun_payment',10,2);
            $table->date('loan_date');
            $table->date('start_payment');
            $table->decimal('late_fee',10,2);
            $table->integer('days_late');
            $table->boolean('pago_automatico');
            $table->decimal('pay_documentation',10,2)->nullable();
            $table->decimal('pay_placa',10,2)->nullable();
            $table->decimal('total_financed',10,2);
            $table->decimal('balance',10,2);
            $table->timestamps();

            //llaves foraneas
            $table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('car_id')->references('id')->on('cars')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::dropIfExists('loans');
    }
}

