<?php

namespace Database\Seeders;

use App\Actions\GenerateBookCode;
use App\Enums\BookLanguage;
use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PublisherSeeder::class,
        ]);

        $generateBookCode = app(GenerateBookCode::class);
        $memberRole = Role::firstOrCreate(['name' => 'member']);
        $fantasyCategory = Category::where('slug', 'fantasi')->first();
        $publisherIds = Publisher::pluck('id')->all();
        $covers = collect(Storage::disk('public')->files('books'))
            ->filter(fn($path) => !str_starts_with(basename($path), '.') &&
                in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
            ->sort()
            ->values();


        User::factory(10)
            ->create()
            ->each(fn(User $user) => $user->assignRole($memberRole));

        if (!$fantasyCategory || $covers->isEmpty() || empty($publisherIds)) {
            return;
        }

        $faker = fake();

        Book::withoutEvents(function () use ($covers, $faker, $fantasyCategory, $publisherIds, $generateBookCode) {
            $covers->each(function (string $cover, int $index) use ($faker, $fantasyCategory, $publisherIds, $generateBookCode) {
                $title = Str::headline(pathinfo($cover, PATHINFO_FILENAME));
                $slug  = Str::slug($title);
                $year  = $faker->numberBetween(2010, now()->year);
                $total = $faker->numberBetween(5, 20);

                $book = Book::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'book_code'        => $generateBookCode($year, $fantasyCategory, $index + 1),
                        'title'            => $title,
                        'author'           => $faker->name(),
                        'publication_year' => $year,
                        'isbn'             => $faker->unique()->isbn13(),
                        'language'         => BookLanguage::INDONESIAN->value,
                        'synopsis'         => $faker->paragraphs(3, true),
                        'number_of_pages'  => $faker->numberBetween(150, 550),
                        'status'           => BookStatus::AVAILABLE->value,
                        'cover'            => $cover,
                        'price'            => $faker->numberBetween(50000, 250000),
                        'category_id'      => $fantasyCategory->id,
                        'publisher_id'     => Arr::random($publisherIds),
                    ]
                );

                $book->stock()->updateOrCreate(
                    ['book_id' => $book->id],
                    [
                        'total'     => $total,
                        'available' => $total,
                        'loaned'    => 0,
                        'lost'      => 0,
                        'damaged'   => 0,
                    ]
                );
            });
        });
    }
}
