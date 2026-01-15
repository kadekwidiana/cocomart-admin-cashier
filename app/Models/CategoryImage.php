<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'category_images';

    protected $fillable = [
        'oxy_category_id',
        'image',
    ];
}
