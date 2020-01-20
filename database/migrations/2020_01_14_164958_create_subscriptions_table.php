<?php

use App\Models\Subscription;
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
            $table->unsignedBigInteger('account');
            $table->string("code")->unique();
            $table->unsignedBigInteger('client_id');
            $table->date('date_start');
            $table->date('date_end')->nullable();
            $table->enum('billing_cycle', ['semanal','mensual','quincenal','anual'])
                  ->default('mensual');
            $table->string('status')->default(Subscription::SUBSCRIPTION_ACTIVE);

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
