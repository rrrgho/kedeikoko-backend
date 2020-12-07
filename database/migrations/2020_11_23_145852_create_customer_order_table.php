<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_order', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->uuid('reseller_uid');
            $table->uuid('customer_uid');
            $table->integer('product_id');
            $table->double('product_price');
            $table->double('tax')->default(0);
            $table->double('purchase_total');
            $table->string('prove_image1')->nullable();
            $table->string('prove_image2')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_order');
    }
}
