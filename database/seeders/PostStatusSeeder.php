<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PostStatus;

class PostStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'published', 'description' => 'Post is publicly visible'],
            ['name' => 'draft', 'description' => 'Post is a draft and not published'],
            ['name' => 'scheduled', 'description' => 'Post is scheduled for future publishing'],
            ['name' => 'review', 'description' => 'Post is under review before publishing'],
            ['name' => 'archived', 'description' => 'Post is archived and hidden'],
            ['name' => 'deleted', 'description' => 'Post is deleted but kept for records'],
        ];

        foreach ($statuses as $status) {
            PostStatus::firstOrCreate(['name' => $status['name']], $status);
        }
    }
}