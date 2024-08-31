<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'offerer_id',
        'offeredBook_id',
        'wishedBook_id',
    ];

    public function offerer()
    {
        return $this->belongsTo(User::class, 'offerer_id');
    }

    public function offeredBook()
    {
        return $this->belongsTo(Book::class, 'offeredBook_id');
    }

    public function wishedBook()
    {
        return $this->belongsTo(Book::class, 'wishedBook_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function exchangeRequests()
    {
        return $this->hasMany(ExchangeRequest::class);
    }
}
