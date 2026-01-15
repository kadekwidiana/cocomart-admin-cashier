<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'promos';

    protected $fillable = [
        'title',
        'code',
        'discount_percentage',
        'is_active',
        'image',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
