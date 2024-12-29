<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleHelperService
{
    /**
     * Get paginated articles with optional filters
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getArticles(array $filters): LengthAwarePaginator
    {
        $query = Article::query()
            ->with(['source', 'categories']);

        $this->applyFilters($query, $filters);

        return $query->orderBy('published_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get a single article by ID with relationships
     *
     * @param Article $article
     * @return Article
     */
    public function getArticle(Article $article): Article
    {
        return $article->load(['source', 'categories']);
    }

    /**
     * Get personalized articles based on user preferences
     *
     * @param array $preferences
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPersonalizedFeed(array $preferences, int $perPage = 15): LengthAwarePaginator
    {
        $query = Article::query()
            ->with(['source', 'categories'])
            ->latest('published_at');

        $this->applyPreferencesFilters($query, $preferences);

        return $query->paginate($perPage);
    }

    /**
     * Apply search filters to the query
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        // Search by keyword in title and content
        if (!empty($filters['keyword'])) {
            $query->whereFullText(['title', 'content'], $filters['keyword']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->where('published_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('published_at', '<=', $filters['date_to']);
        }

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        // Filter by source
        if (!empty($filters['source_id'])) {
            $query->where('source_id', $filters['source_id']);
        }

        // Filter by author
        if (!empty($filters['author'])) {
            $query->where('author', 'like', "%{$filters['author']}%");
        }
    }

    /**
     * Apply user preferences filters to the query
     *
     * @param Builder $query
     * @param array $preferences
     * @return void
     */
    private function applyPreferencesFilters(Builder $query, array $preferences): void
    {
        // Filter by preferred categories
        if (!empty($preferences['preferred_categories'])) {
            $query->whereHas('categories', function ($q) use ($preferences) {
                $q->whereIn('categories.id', $preferences['preferred_categories']);
            });
        }

        // Filter by preferred sources
        if (!empty($preferences['preferred_sources'])) {
            $query->whereIn('source_id', $preferences['preferred_sources']);
        }

        // Filter by preferred authors
        if (!empty($preferences['preferred_authors'])) {
            $query->whereIn('author', $preferences['preferred_authors']);
        }
    }

}