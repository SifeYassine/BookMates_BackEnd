<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'notification_sent',
        'requester_id',
        'bookPost_id',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function bookPost()
    {
        return $this->belongsTo(BookPost::class, 'bookPost_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
