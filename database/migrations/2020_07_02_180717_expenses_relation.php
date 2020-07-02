<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExpensesRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         //  This is Realations for the operation_expenses Table ..
         Schema::table('operation_expenses', function (Blueprint $table) {
            $table->foreign('operation_id')->references('id')->on('operations');
            $table->foreign('expenses_type_id')->references('id')->on('expenses');
            $table->foreign('provider_type_id')->references('id')->on('expenses_provider_types');
            $table->foreign('currency_id')->references('id')->on('currencies');
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
    }
}
