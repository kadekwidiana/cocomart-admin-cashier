<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up(): void
    {
        Schema::table('transaction_shipments', function (Blueprint $table) {
            $table->string('grab_delivery_id')->nullable()->change();
            $table->decimal('grab_shipping_cost', 12, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_shipments', function (Blueprint $table) {
            $table->string('grab_delivery_id')->nullable(false)->change();
            $table->decimal('grab_shipping_cost', 12, 2)->default(null)->change();
        });
    }
};
