<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    protected $fillable = [
        'loan_code',
        'user_id',
        'book_id',
        'loan_date',
        'due_date',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'due_date'  => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function returnBook(): HasOne
    {
        return $this->hasOne(ReturnBook::class);
    }

    public static function checkLoanBook(int $user_id, int $book_id): bool
    {
        return self::query()
            ->where('user_id', $user_id)
            ->where('book_id', $book_id)
            ->whereDoesntHave('returnBook')
            ->exists();
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny([
                    'loan_code',
                    'loan_date',
                    'due_date',
                ], "ILIKE", "%{$search}%");
            });
        });
    }

    public function scopeSorting(Builder $query, array $sorts): void
    {
        $query->when($sorts['field'] ?? null && $sorts['direction'] ?? null, function ($query) use ($sorts) {
            $query->orderBy($sorts['field'], $sorts['direction']);
        });
    }

    public static function totalLoanBooks(): array
    {
        return [
            'days'   => self::whereDate('loan_date', Carbon::now()->toDateString())->count(),
            'weeks'  => self::whereBetween('loan_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'months' => self::whereMonth('loan_date', Carbon::now()->month)
                ->whereYear('loan_date', Carbon::now()->year)->count(),
            'years'  => self::whereYear('loan_date', Carbon::now()->year)->count(),

        ];
    }

    public static function recentForUser($user, int $limit = 5)
    {
        $query = self::select('id', 'loan_code', 'book_id')
            ->with(['book:id,title'])
            ->latest('created_at')
            ->limit($limit);

        if (! $user->hasAnyRole(['admin', 'operator'])) {
            $query->where('user_id', $user->id);
        }

        return $query->get();
    }

    public static function countForUser($user): int
    {
        $query = self::query();

        if (! $user->hasAnyRole(['admin', 'operator'])) {
            $query->where('user_id', $user->id);
        }

        return $query->count();
    }
}
