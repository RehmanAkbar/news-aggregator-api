<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Source;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Seeder;
use App\Services\NewsAPIAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\NYTimesAPIAdapter;
use Illuminate\Support\Facades\Log;
use App\Services\GuardianAPIAdapter;
use Illuminate\Support\Facades\Schema;

class ArticleSeeder extends Seeder
{
    private const BATCH_SIZE = 10;
    private array $newsAPIAdapters = [];
    private Collection $articles;
    private Collection $categories;

    public function __construct()
    {
        $this->articles = new Collection();
        $this->initializeAPIAdapters();
    }

    public function run(): void
    {
        try {
            $this->truncateArticles();
            $this->loadCategories();
            $this->fetchAndSeedArticles();
            
            $this->command->info('Articles seeded successfully!');
        } catch (\Exception $e) {
            Log::error('Article seeding failed: ' . $e->getMessage());
            $this->command->error('Article seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function initializeAPIAdapters(): void
    {
        $this->newsAPIAdapters = [
            'NewsAPI' => new NewsAPIAdapter(),
            'The Guardian' => new GuardianAPIAdapter(),
            'New York Times' => new NYTimesAPIAdapter(),
        ];
    }

    private function truncateArticles(): void
    {
        Schema::disableForeignKeyConstraints();
        Article::truncate();
        DB::table('article_category')->truncate();
        Schema::enableForeignKeyConstraints();
    }

    private function loadCategories(): void
    {
        $this->categories = Category::all();
    }

    private function fetchAndSeedArticles(): void
    {
        Source::active()->each(function (Source $source) {
            $this->processSource($source);
        });
    }

    private function processSource(Source $source): void
    {
        if (!isset($this->newsAPIAdapters[$source->name])) {
            Log::warning("No adapter found for source: {$source->name}");
            return;
        }

        $adapter = $this->newsAPIAdapters[$source->name];
        
        foreach ($this->categories as $category) {
            try {
                $articles = $adapter->fetchArticles($source, $category->name);
                if ($articles && !empty($articles)) {
                    $this->processArticles($articles, $source->id, $category->id);
                }
            } catch (\Exception $e) {
                Log::error("Error fetching articles for {$source->name} - {$category->name}: " . $e->getMessage());
                // Continue with next category
                continue; 
            }
        }
    }

    private function processArticles(array $articles, int $sourceId, int $categoryId): void
    {
        $transformedArticles = array_map(
            fn($article) => $this->transformArticle($article, $sourceId),
            $articles
        );

        collect($transformedArticles)
            ->chunk(self::BATCH_SIZE)
            ->each(function ($chunk) use ($categoryId, $sourceId) {
                try {
                    DB::beginTransaction();
                    
                    DB::table('articles')->insert($chunk->toArray());
                    
                    $insertedIds = DB::table('articles')
                        ->whereIn('title', $chunk->pluck('title'))
                        ->where('source_id', $sourceId)
                        ->orderBy('id', 'desc')
                        ->limit($chunk->count())
                        ->pluck('id')
                        ->toArray();
                    
                    $this->attachCategories($insertedIds, $categoryId);
                    
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Failed to process articles batch: " . $e->getMessage());
                    throw $e;
                }
            });
    }

    private function transformArticle(array $article, int $sourceId): array
    {
        $publishedAt = $this->parsePublishedDate(
            $article['publishedAt'] ?? 
            $article['webPublicationDate'] ?? 
            null
        );

        return [
            'source_id' => $sourceId,
            'title' => substr($article['title'] ?? $article['webTitle'] ?? '', 0, 255),
            'content' => $article['description'] ?? $article['abstract'] ?? '',
            'url' => substr($article['url'] ?? $article['webUrl'] ?? '', 0, 255),
            'published_at' => $publishedAt,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function parsePublishedDate(?string $date): string
    {
        if (empty($date)) {
            return now()->format('Y-m-d H:i:s');
        }

        try {
            return Carbon::parse($date)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            Log::warning("Invalid date format: {$date}");
            return now()->format('Y-m-d H:i:s');
        }
    }

    private function attachCategories(array $articleIds, int $categoryId): void
    {
        $categoryAttachments = array_map(
            fn($articleId) => [
                'article_id' => $articleId,
                'category_id' => $categoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            $articleIds
        );

        DB::table('article_category')->insert($categoryAttachments);
    }
}