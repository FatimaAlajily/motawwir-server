<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'user_name' => 'noran salih',
            'email' => 'noran@gmail.com',
            'password' => '1234',
            'role' => 'company',
            'avatar' => null,
            'votra' => 0,
        ]);


        User::create([
            'user_name' => 'Asmaa salih',
            'email' => 'asmaa@gmail.com',
            'password' => '1234',
            'role' => 'admin',
            'avatar' => null,
            'votra' => 0,
        ]);

        User::create([
            'user_name' => 'Fatima salih',
            'email' => 'fatima@gmail.com',
            'password' => '1234',
            'role' => 'admin',
            'avatar' => null,
            'votra' => 0,
        ]);
    }
}
