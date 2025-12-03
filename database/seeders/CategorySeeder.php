<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Fiksi',
                'cover'       => 'categories/fiksi.jpg',
                'description' => 'Cerita imajinatif yang mengajak pembaca menjelajahi dunia dan karakter karangan dengan konflik serta petualangan yang memikat.',
            ],
            [
                'name'        => 'Non-Fiksi',
                'cover'       => 'categories/non fiksi.jpg',
                'description' => 'Buku berbasis fakta yang menyajikan informasi, wawasan, dan kisah nyata untuk menambah pengetahuan serta perspektif baru.',
            ],
            [
                'name'        => 'Ilmu Pengetahuan',
                'cover'       => 'categories/Ilmu pengetahuan.jpg',
                'description' => 'Rangkaian karya yang mengulas teori, riset, dan fenomena alam maupun sosial secara sistematis untuk memuaskan rasa ingin tahu.',
            ],
            [
                'name'        => 'Sejarah',
                'cover'       => 'categories/sejarah.jpg',
                'description' => 'Kisah masa lalu yang merekam peristiwa penting, tokoh berpengaruh, serta pelajaran yang membentuk peradaban hingga hari ini.',
            ],
            [
                'name'        => 'Biografi',
                'cover'       => 'categories/Bio.jpg',
                'description' => 'Potret perjalanan hidup tokoh inspiratif, lengkap dengan perjuangan, pencapaian, dan nilai yang dapat dipelajari pembaca.',
            ],
            [
                'name'        => 'Anak-anak',
                'cover'       => 'categories/Anak.jpg',
                'description' => 'Bacaan ramah anak dengan bahasa ringan, ilustrasi menarik, serta pesan moral yang membantu tumbuh kembang imajinasi.',
            ],
            [
                'name'        => 'Teknologi',
                'cover'       => 'categories/Teknologi.jpg',
                'description' => 'Topik seputar inovasi digital, perangkat, dan tren industri yang menjelaskan cara teknologi memengaruhi kehidupan sehari-hari.',
            ],
            [
                'name'        => 'Misteri',
                'cover'       => 'categories/misteri.jpg',
                'description' => 'Cerita penuh teka-teki, ketegangan, dan investigasi yang menantang pembaca menebak kebenaran di balik setiap petunjuk.',
            ],
            [
                'name'        => 'Fantasi',
                'cover'       => 'categories/fantasy.jpg',
                'description' => 'Dunia penuh sihir, makhluk legendaris, dan petualangan epik yang menawarkan pelarian ke realitas alternatif nan menawan.',
            ],
            [
                'name'        => 'Pengembangan Diri',
                'cover'       => 'categories/pengembangan diri.jpg',
                'description' => 'Panduan praktis untuk meningkatkan kebiasaan, pola pikir, dan keterampilan agar pembaca berkembang di berbagai aspek kehidupan.',
            ],
        ];

        collect($categories)->each(function (array $category) {
            Category::updateOrCreate(
                ['slug' => $slug = Str::slug($category['name'])],
                [
                    'name'        => $category['name'],
                    'slug'        => $slug,
                    'description' => $category['description'],
                    'cover'       => $category['cover'],
                ]
            );
        });
    }
}
