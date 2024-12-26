<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Source;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use App\Services\NewsAPIAdapter;
use App\Services\GuardianAPIAdapter;
use App\Services\NYTimesAPIAdapter;

class ArticleService
{
    private array $adapters;
    
    public function __construct(
        GuardianAPIAdapter $guardianAPIAdapter,
        NewsAPIAdapter $newsAPIAdapter,
        NYTimesAPIAdapter $nyTimesAPIAdapter
    ) {
        $this->adapters = [
            'NewsAPI' => $newsAPIAdapter,
            'The Guardian' => $guardianAPIAdapter,
            'New York Times' => $nyTimesAPIAdapter
        ];
    }

    /**
     * Fetch and store articles from all active sources for each category
     *
     * @return array Statistics about the fetching process
     */
    public function fetchAndStoreArticles(): array
    {
        $stats = [
            'total_processed' => 0,
            'successful' => 0,
            'failed' => 0,
            'sources' => []
        ];

        $sources = Source::where('is_active', true)->get();
        $categories = Category::all();

        foreach ($sources as $source) {
            $stats['sources'][$source->name] = [
                'processed' => 0,
                'successful' => 0,
                'failed' => 0
            ];

            $adapter = $this->adapters[$source->name] ?? null;
            if (!$adapter) {
                Log::error("No adapter found for source: {$source->name}");
                continue;
            }

            foreach ($categories as $category) {
                try {
                    $articles = $adapter->fetchArticles($source, $category->name);
                    if ($articles) {
                        $this->processArticles($articles, $source, $category);
                        $stats['sources'][$source->name]['successful']++;
                    } else {
                        $stats['sources'][$source->name]['failed']++;
                    }
                    $stats['sources'][$source->name]['processed']++;
                } catch (\Exception $e) {
                    Log::error("Error fetching articles from {$source->name} for category {$category->name}: " . $e->getMessage());
                    $stats['sources'][$source->name]['failed']++;
                }
            }

            $stats['total_processed'] += $stats['sources'][$source->name]['processed'];
            $stats['successful'] += $stats['sources'][$source->name]['successful'];
            $stats['failed'] += $stats['sources'][$source->name]['failed'];
        }

        return $stats;
    }

    /**
     * Process and store articles from API response
     *
     * @param array $articles
     * @param Source $source
     * @param Category $category
     * @return void
     */
    private function processArticles(array $articles, Source $source, Category $category): void
    {
        foreach ($articles as $articleData) {
            try {
                $article = $this->createArticle($articleData, $source);
                $article->categories()->syncWithoutDetaching([$category->id]);
            } catch (\Exception $e) {
                Log::error("Error processing article: " . $e->getMessage());
            }
        }
    }

    /**
     * Create or update an article based on URL uniqueness
     *
     * @param array $data
     * @param Source $source
     * @return Article
     */
    private function createArticle(array $data, Source $source): Article
    {
        $article = new Article();
        
        $article->fill([
            'source_id' => $source->id,
            'title' => $data['title'],
            'content' => $data['content'] ?? '',
            'author' => $data['author'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'published_at' => $data['published_at'] ?? now(),
        ]);

        $article->save();
        return $article;
    }
}