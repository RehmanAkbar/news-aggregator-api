<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("sources")->truncate();
        $sources = [
            [
                'name' => config('news-sources.newsapi.name'),
                'base_url' => config('news-sources.newsapi.base_url'),
                'api_key' => config('news-sources.newsapi.api_key'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => config('news-sources.guardian.name'),
                'base_url' => config('news-sources.guardian.base_url'),
                'api_key' => config('news-sources.guardian.api_key'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => config('news-sources.nytimes.name'),
                'base_url' => config('news-sources.nytimes.base_url'),
                'api_key' => config('news-sources.nytimes.api_key'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($sources as $source) {
            DB::table('sources')->insert($source);
        }

        $this->command->info('News sources seeded successfully!');
    }
}