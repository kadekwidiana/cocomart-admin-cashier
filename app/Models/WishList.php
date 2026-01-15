<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WishList extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'wish_lists';

    protected $fillable = [
        'oxy_customer_id',
        'oxy_item_master_id',
    ];
}
