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
        'status',
        'grab_json_response',
    ];

    protected $casts = [
        'status' => TransactionShipmentStatus::class,
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
