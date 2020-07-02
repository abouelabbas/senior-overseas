<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operation_id')->unsigned()->nullable();
            $table->integer('expenses_type_id')->unsigned()->nullable();
            $table->double('buy',8, 2)->nullable();
            $table->double('sell',8, 2)->nullable();
            $table->integer('provider_type_id')->unsigned()->nullable();
            $table->integer('currency_id')->unsigned()->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('operation_expenses');
    }
}
