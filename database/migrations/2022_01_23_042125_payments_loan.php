<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PaymentsLoan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('payments_loan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->unsignedBigInteger('user_id');
            $table->string('description');
            $table->integer('concepto');
            $table->decimal('monto',10,2);
            $table->date('date_doit')->nullable();
            $table->integer('forma_pago')->nullable();
            $table->decimal('balance',10,2);
            $table->integer('estado')->default(1);
            $table->string('reason_delete')->nullable();
            $table->timestamps();

            $table->foreign('loan_id')->references('id')->on('loans')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });

        //1=tarjeta de debito; 2= tarjeta de credito; 3=cheque; 4= cash; 5=zelle; 6=deposit account
        DB::statement('ALTER TABLE payments_loan ADD CONSTRAINT chk_forma_pago CHECK (forma_pago between 1 and 6);');
        //1=pago que se va a financiar; 2=pago de downpayment; 3=payment of the loan (-); 4=late fee (+);5=discount for pay the balance
        DB::statement('ALTER TABLE payments_loan ADD CONSTRAINT chk_concepto_payment CHECK (concepto between 1 and 4);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('payments_loan');
    }
}
