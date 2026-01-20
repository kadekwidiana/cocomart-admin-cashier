<?php

namespace App\Models;

use App\Enums\TransactionPickupStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionPickup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaction_pickups';

    protected $fillable = [
        'transaction_id',
        'pickup_code',
        'pickup_time',
        'pickup_end_time',
        'receiver_name',
        'receiver_phone_number',
        'status',
    ];

    protected $casts = [
        'status' => TransactionPickupStatus::class,
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
