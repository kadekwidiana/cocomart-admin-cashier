<?php

namespace App\Models;

use App\Enums\TransactionFulfillmentType;
use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transactions';

    protected $fillable = [
        'id',
        'oxy_customer_id',
        'oxy_location_id',
        'status',
        'fulfillment_type',
        'payment_token',
        'subtotal',
        'discount',
        'shipping_cost',
        'total',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'status' => TransactionStatus::class,
        'fulfillment_type' => TransactionFulfillmentType::class
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Relation to TransactionShipment
     */
    public function shipment()
    {
        return $this->hasOne(TransactionShipment::class);
    }

    /**
     * Relation to TransactionPickup
     */
    public function pickup()
    {
        return $this->hasOne(TransactionPickup::class);
    }
}
