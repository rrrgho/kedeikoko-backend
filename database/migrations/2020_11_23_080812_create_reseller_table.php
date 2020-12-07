<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResellerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('reseller')){
            Schema::create('reseller', function (Blueprint $table) {
                if(config('database.default') == 'pgsql')
                    $table->uuid('uid')->default(DB::raw('gen_uuid()'))->primary();
                else if(config('database.default') == 'mysql')
                    $table->uuid('uid')->default(DB::raw('uuid()'))->primary();
                $table->string('name');
                $table->string('national_id')->nullable()->unique();
                $table->text('address')->nullable();
                $table->text('phone')->nullable()->unique();
                $table->text('selfie_image')->nullable();
                $table->text('idcard_image')->nullable();
                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists('reseller');
    }
}
