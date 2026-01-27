<?php

namespace App\Models;

use App\Enums\NotificationType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notifications';

    protected $fillable = [
        'title',
        'image',
        'body',
        'type',
    ];

    protected $casts = [
        'type' => NotificationType::class,
    ];

    public function notificationReadOne()
    {
        return $this->hasOne(NotificationRead::class, 'notification_id', 'id');
    }

    /**
     * Accessor to format the created_at attribute.
     *
     * @param  string $value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
