<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Str;

class RunningSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'day',
        'workout',
        'distance',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($schedule) {
            $schedule->slug = Str::slug($schedule->workout . '-' . uniqid());
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
