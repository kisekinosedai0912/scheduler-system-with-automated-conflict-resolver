<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'event_id',
        'user_id',
        'is_read',
    ];

    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
