<?php

namespace App\Models;

use App\Enums\TransactionShipmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionShipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaction_shipments';

    protected $fillable = [
        'transaction_id',
        'grab_delivery_id',
        'grab_shipping_cost',
        'grab_vehicle_type',
        'grab_service_type',
        'status',
        'receiver_name',
        'receiver_phone_number',
        'receiver_address',
        'receiver_latitude',
        'receiver_longitude',
        'grab_json_response',
    ];

    protected $casts = [
        'status' => TransactionShipmentStatus::class,
        'grab_shipping_cost' => 'decimal:2',
        'receiver_latitude' => 'decimal:14',
        'receiver_longitude' => 'decimal:14',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
