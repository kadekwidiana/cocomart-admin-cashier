<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImageSlider extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'image_sliders';

    protected $fillable = [
        'image',
        'link',
        'index',
        'is_active',
    ];

    protected $casts = [
        'index' => 'integer',
        'is_active' => 'boolean',
    ];
}
