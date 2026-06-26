<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Menghapus data user lama ber-username admin jika ada, agar tidak duplikat
        User::where('username', 'admin')->delete();

        User::create([
            'name' => 'Administrator Losari',
            'username' => 'admin',
            'password' => Hash::make('admin'),
        ]);
    }
}