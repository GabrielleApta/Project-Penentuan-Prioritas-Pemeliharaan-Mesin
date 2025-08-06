<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder database.
     */
    public function run(): void
    {
        // Buat Regu Mekanik
        User::updateOrCreate(
            ['email' => 'regumekanik@gmail.com'],
            [
                'name' => 'Regu Mekanik',
                'password' => Hash::make('regumekanik'),
                'role' => 'regu_mekanik',
                'photo' => null,
            ]
        );

        // Buat Koordinator Mekanik
        User::updateOrCreate(
            ['email' => 'koordinatormekanik@gmail.com'],
            [
                'name' => 'Koordinator Mekanik',
                'password' => Hash::make('koordinatormekanik'),
                'role' => 'koordinator_mekanik',
                'photo' => null,
            ]
        );
    }
}
