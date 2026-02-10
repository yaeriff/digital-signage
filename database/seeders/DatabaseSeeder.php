<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Kode ini akan cek: Kalau email admin@gmail.com belum ada, buatkan barunya.
        // Kalau sudah ada, biarkan saja (jangan error).
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'], // Cek email ini
            [
                'name' => 'Admin Ganteng',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
    }
}