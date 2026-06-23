<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_shipments', function (Blueprint $table) {
            $table->string('grab_vehicle_type')->nullable()->after('grab_shipping_cost');
            $table->string('grab_service_type')->nullable()->after('grab_vehicle_type');
            $table->decimal('receiver_latitude', 17, 14)->nullable()->after('receiver_address');
            $table->decimal('receiver_longitude', 17, 14)->nullable()->after('receiver_latitude');
        });
    }

    public function down(): void
    {
        Schema::table('transaction_shipments', function (Blueprint $table) {
            $table->dropColumn([
                'grab_vehicle_type',
                'grab_service_type',
                'receiver_latitude',
                'receiver_longitude',
            ]);
        });
    }
};
