<?php

namespace App\Models;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationTarget extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'seen' => 'boolean',
    ];


    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function targetable()
    {
        return $this->morphTo();
    }
}
