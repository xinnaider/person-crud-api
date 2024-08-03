<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Person;

class UserAdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $person = Person::create([
            'name' => 'John Doe',
            'title' => 'Mr.',
            'birth_date' => '1990-01-01',
            'relationship' => 'single',
        ]);

        User::create([
            'email' => 'admin@admin.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
            'person_id' => $person->id,
        ]);
    }
}
