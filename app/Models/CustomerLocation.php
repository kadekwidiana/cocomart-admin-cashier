<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_locations';

    protected $fillable = [
        'oxy_customer_id',
        'latitude',
        'longitude',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}
