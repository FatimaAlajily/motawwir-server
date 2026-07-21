<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        Post::insert([
            [
                'title' => 'How can I learn Laravel in the best way?',
                'content' => 'I have experience with PHP and want to become a Laravel backend developer. What learning roadmap do you recommend?',
                'type' => 'question',
                'user_id' => $users->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Work
            [
                'title' => 'Backend Laravel Developer Needed',
                'content' => 'Our company is looking for a Laravel Backend Developer with at least 2 years of experience.',
                'file' => null,
                'skill' => 'Laravel',
                'primary_link' => 'https://company.com/jobs',
                'secondary_link' => null,
                'type' => 'work',
                'user_id' => $users->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // News
            [
                'title' => 'Laravel 13 Released',
                'file' => null,
                'primary_link' => 'https://laravel.com',
                'type' => 'new',
                'user_id' => $users->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Project
            [
                'title' => 'Open Source Hospital Management System',
                'content' => 'Looking for contributors to help develop an open-source hospital management system using Laravel and Flutter.',
                'file' => null,
                'skill' => 'Laravel, Flutter',
                'primary_link' => 'https://github.com/example/project',
                'secondary_link' => 'https://trello.com/example',
                'type' => 'project',
                'user_id' => $users->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Team
            [
                'title' => 'Looking for Flutter UI Designer',
                'content' => 'We are building a graduation project and need a Flutter developer experienced in UI implementation.',
                'skill' => 'Flutter',
                'primary_link' => 'https://discord.gg/example',
                'type' => 'team',
                'user_id' => $users->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],


        ]);
    }
}
