<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Branches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('name',45);
            $table->string('address_p',250);
            $table->string('address_s',150)->nullable();
            $table->char('telephone',10);
            $table->char('cellphone',10);
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('restrict');
            $table->timestamps();
        });

        if(Schema::hasTable('cars'))
        {
            Schema::table('cars', function (Blueprint $table) {
                $table->foreign('branch_id')->references('id')->on('branches')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::dropIfExists('branches');
    }
}
