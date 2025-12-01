<?php

namespace App\Models;

use App\Enums\BookLanguage;
use App\Enums\BookStatus;
use App\Observers\BookObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy(BookObserver::class)]
class Book extends Model
{
    protected $fillable = [
        'book_code',
        'title',
        'slug',
        'author',
        'publication_year',
        'isbn',
        'language',
        'synopsis',
        'number_of_pages',
        'status',
        'cover',
        'price',
        'category_id',
        'publisher_id',
    ];

    protected function casts(): array
    {
        return [
            'language' => BookLanguage::class,
            'status'   => BookStatus::class,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny([
                    'book_code',
                    'title',
                    'slug',
                    'author',
                    'publication_year',
                    'isbn',
                    'language',
                    'status',
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

    public function updateStock(string $columnToDecrement, string $columnToIncrement): bool
    {
        if (!$this->stock) {
            return false;
        }

        $decrementValue = max(0, $this->stock->$columnToDecrement - 1);
        $incrementValue = $this->stock->$columnToIncrement + 1;

        if ($this->stock->$columnToDecrement <= 0) {
            return false;
        }

        return (bool) $this->stock()->update([
            $columnToDecrement => $decrementValue,
            $columnToIncrement => $incrementValue,
        ]);
    }

    public function stock_loan()
    {
        return $this->updateStock('available', 'loaned');
    }

    public function stock_return_loan()
    {
        return $this->updateStock('loaned', 'available');
    }

    public function stock_damaged()
    {
        return $this->updateStock('loaned', 'damaged');
    }

    public function stock_lost()
    {
        return $this->updateStock('loaned', 'lost');
    }

    public static function leastLoanedBooks(int $limit = 5)
    {
        return self::select(['id', 'title', 'author'])
            ->withCount('loans')
            ->orderBy('loans_count', 'asc')
            ->limit($limit)
            ->get();
    }

    public static function mostLoanedBooks(int $limit = 5)
    {
        return self::select(['id', 'title', 'author'])
            ->withCount('loans')
            ->orderBy('loans_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
