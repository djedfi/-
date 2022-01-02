<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Cars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('make_id');
            $table->unsignedBigInteger('model_id');
            $table->unsignedBigInteger('trim_id');
            $table->unsignedBigInteger('style_id');
            $table->unsignedBigInteger('branch_id');
            $table->string('vin',17)->unique();
            $table->string('stock_number',10)->unique();
            $table->year('year');
            $table->decimal('precio',10,2);
            $table->integer('doors');
            $table->integer('mileage');
            $table->integer('transmission');
            $table->integer('condition_car');
            $table->integer('fuel_type');
            $table->string('fuel_economy',45)->nullable();
            $table->string('engine',45)->nullable();
            $table->string('drivetrain',45)->nullable();
            $table->string('wheel_size',45)->nullable();
            $table->string('url_info',150)->nullable();

            //llaves foraneas
            $table->foreign('make_id')->references('id')->on('makes')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('model_id')->references('id')->on('models')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('trim_id')->references('id')->on('trims')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('style_id')->references('id')->on('styles')->onUpdate('cascade')->onDelete('restrict');

            $table->timestamps();
        });
        //1=Automatic; 2= CVT; 3= Manual
        DB::statement('ALTER TABLE cars ADD CONSTRAINT chk_transmission CHECK (transmission between 1 and 3);');
        //1=Used; 2= New
        DB::statement('ALTER TABLE cars ADD CONSTRAINT chk_condition_car CHECK (condition_car = 1 or condition_car = 2);');
        //1=Gasoline;2=Diesel
        DB::statement('ALTER TABLE cars ADD CONSTRAINT chk_fuel_type CHECK (fuel_type = 1 or fuel_type = 2);');


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('cars');
    }
}
