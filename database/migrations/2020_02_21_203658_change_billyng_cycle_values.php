<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeBillyngCycleValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE subscriptions DROP CONSTRAINT subscriptions_billing_cycle_check");
        DB::statement("UPDATE subscriptions SET billing_cycle = 'Semanal' WHERE billing_cycle = 'semanal'");
        DB::statement("UPDATE subscriptions SET billing_cycle = 'Quincenal' WHERE billing_cycle = 'quincenal'");
        DB::statement("UPDATE subscriptions SET billing_cycle = 'Mensual' WHERE billing_cycle = 'mensual'");
        DB::statement("UPDATE subscriptions SET billing_cycle = 'Anual' WHERE billing_cycle = 'anual'");
        DB::statement("ALTER TABLE subscriptions ADD CONSTRAINT subscriptions_billing_cycle_check CHECK (billing_cycle::text = ANY (ARRAY['Semanal'::character varying, 'Mensual'::character varying, 'Quincenal'::character varying, 'Anual'::character varying]::text[]))");
        DB::statement("ALTER TABLE subscriptions ALTER COLUMN billing_cycle SET DEFAULT 'Mensual'");
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
