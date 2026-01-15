<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'store_images';

    protected $fillable = [
        'oxy_store_id',
        'image',
    ];
}
