<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::create([
            'title' => 'First Post',
            'content' => 'This is a test post',
            'skill' => 'Laravel',
            'primary_link' => 'https://example.com',
            'type' => 'question',
            'user_id' => 3,
        ]);
    }
}
