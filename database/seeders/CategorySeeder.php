<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        Schema::disableForeignKeyConstraints();
        
        DB::table('categories')->truncate();

        $categories = [
            [
                'name' => 'business',
                'description' => 'Business, finance, and economy news',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'technology',
                'description' => 'Technology, gadgets, and digital trends',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'science',
                'description' => 'Scientific discoveries and research',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'health',
                'description' => 'Health, medicine, and wellness',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'sports',
                'description' => 'Sports news and athletics',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'entertainment',
                'description' => 'Entertainment, movies, and culture',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'politics',
                'description' => 'Political news and current affairs',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'world',
                'description' => 'International news and global events',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'environment',
                'description' => 'Environmental news and climate change',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'education',
                'description' => 'Education and learning news',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('categories')->insert($categories);

        $this->command->info('Categories seeded successfully!');
    }
}