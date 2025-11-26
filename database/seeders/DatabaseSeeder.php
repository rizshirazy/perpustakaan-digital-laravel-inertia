<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name'     => $name = 'Bang Iky',
            'username' => usernameGenerator($name),
            'email'    => 'iky@cendikia.com',
        ])->assignRole(Role::create(['name' => 'admin']));

        User::factory()->create([
            'name'     => $name = 'Olivia',
            'username' => usernameGenerator($name),
            'email'    => 'olivia@cendikia.com',
        ])->assignRole(Role::create(['name' => 'operator']));

        User::factory()->create([
            'name'     => $name = 'Maia',
            'username' => usernameGenerator($name),
            'email'    => 'maia@cendikia.com',
        ])->assignRole(Role::create(['name' => 'member']));

        User::factory(10)->create();

        $this->call([
            CategorySeeder::class,
            PublisherSeeder::class,
        ]);
    }
}
