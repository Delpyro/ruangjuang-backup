<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; // Wajib ditambahkan untuk membuat slug otomatis

class OwnerUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = 'Super Owner';

        User::updateOrCreate(
            ['email' => 'owner@ruangjuang.com'], // Ganti dengan email yang kamu inginkan
            [
                'name' => $name,
                'slug' => Str::slug($name), // Menambahkan slug otomatis dari nama
                'password' => Hash::make('password123'), 
                'role' => 'owner', 
                'is_active' => true, // Menambahkan status aktif sekalian (opsional tapi disarankan)
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('User Owner berhasil ditambahkan!');
    }
}