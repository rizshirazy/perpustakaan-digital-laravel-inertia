<?php

namespace App\Models;

use App\Enums\FinePaymentStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    protected $fillable = [
        'return_book_id',
        'user_id',
        'late_fee',
        'other_fee',
        'total_fee',
        'fine_date',
        'payment_status',
    ];

    protected $casts = [
        'fine_date'      => 'date',
        'payment_status' => FinePaymentStatus::class,
    ];

    public function returnBook(): BelongsTo
    {
        return $this->belongsTo(ReturnBook::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function totalFines(): array
    {
        return [
            'days'   => self::whereDate('fine_date', Carbon::now()->toDateString())->count(),
            'weeks'  => self::whereBetween('fine_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'months' => self::whereMonth('fine_date', Carbon::now()->month)
                ->whereYear('fine_date', Carbon::now()->year)->count(),
            'years'  => self::whereYear('fine_date', Carbon::now()->year)->count(),

        ];
    }
}
