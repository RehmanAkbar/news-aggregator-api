<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Services\ArticleService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $this->truncateArticles();

            $articleService = app(ArticleService::class);

            $stats = $articleService->fetchAndStoreArticles();

            $this->command->info('Articles seeded successfully!');
            $this->command->info("Total processed: {$stats['total_processed']}");
            $this->command->info("Successful: {$stats['successful']}");
            $this->command->info("Failed: {$stats['failed']}");
        } catch (\Exception $e) {
            Log::error('Article seeding failed: ' . $e->getMessage());
            $this->command->error('Article seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Empties the articles table (and pivot) before seeding.
     */
    private function truncateArticles(): void
    {
        Schema::disableForeignKeyConstraints();
        Article::truncate();
        DB::table('article_category')->truncate();
        Schema::enableForeignKeyConstraints();
    }
}
