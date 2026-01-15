<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemMasterImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'item_master_images';

    protected $fillable = [
        'oxy_item_master_id',
        'image',
    ];
}
