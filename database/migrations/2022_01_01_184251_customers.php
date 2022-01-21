<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Customers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('state_id');
            $table->char('customer_id',9)->unique();
            $table->string('licence',15)->unique();
            $table->string('state_licence',45);
            $table->string('first_name',250);
            $table->string('last_name',250);
            $table->string('initial',4)->nullable();
            $table->string('address_p',250);
            $table->string('address_s',150)->nullable();
            $table->string('city',100);
            $table->string('zip',10);
            $table->string('telephone_res',20)->nullable();
            $table->string('telephone_bus',20)->nullable();
            $table->char('cellphone',10);
            $table->string('email',150)->unique();
            $table->date('birthday');
            $table->integer('gender');
            $table->string('ssn',150)->unique();
            $table->foreign('state_id')->references('id')->on('states')->onUpdate('cascade')->onDelete('restrict');

            $table->timestamps();
        });

        //1=Female; 2= Male; 3= Other
        DB::statement('ALTER TABLE customers ADD CONSTRAINT chk_gender CHECK (gender between 1 and 3);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('customers');
    }
}
