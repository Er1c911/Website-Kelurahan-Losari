<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus user admin lama maupun yang baru agar seed tetap idempoten.
        User::whereIn('username', ['admin', 'losariAdmin'])->delete();

        User::create([
            'name' => 'Administrator Losari',
            'username' => 'losariAdmin',
            'password' => Hash::make('17.Losari.Singosari.'),
        ]);
    }
}