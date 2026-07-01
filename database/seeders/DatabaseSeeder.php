<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Fatima Al-Ibrahim',
            'email' => 'fatema.a@6degrees.com.sa',
            'password' => bcrypt('123f'),
            'role' => 'admin',
            'phone' => '0539052895',
            'department' => 'Software Development',
            'bio' => 'Admin that manages the team members.',
        ]);
    }
}
