<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $fillable = [
        'book_id',
        'total',
        'available',
        'loaned',
        'lost',
        'damaged',
    ];

    protected $casts = [
        'total'     => 'integer',
        'available' => 'integer',
        'loaned'    => 'integer',
        'lost'      => 'integer',
        'damaged'   => 'integer',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
