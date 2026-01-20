<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'location_images';

    protected $fillable = [
        'oxy_location_id',
        'image',
    ];
}
