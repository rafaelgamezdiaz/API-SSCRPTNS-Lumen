<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("code")->nullable();
            $table->unsignedBigInteger('client_id');
            $table->date('date_start');
            $table->date('date_end')->nullable();
            $table->enum('payment_periodicity', ['semanal','mensual','quincenal','anual'])
                  ->default('mensual');
            $table->date('last_billing')->nullable();
            $table->boolean('active')->default(true);

            $table->softDeletes();
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
        Schema::dropIfExists('subscriptions');
    }
}
