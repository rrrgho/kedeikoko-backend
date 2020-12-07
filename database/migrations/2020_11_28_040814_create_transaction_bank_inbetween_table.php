<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionBankInbetweenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_bank_inbetween', function (Blueprint $table) {
            $table->id();
            $table->integer('thisbank_id');
            $table->integer('joinbank_id');
            $table->double('amount');
            $table->string('type');
            $table->string('about');
            $table->string('status');
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
        Schema::dropIfExists('transaction_bank_inbetween');
    }
}
