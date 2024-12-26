<?php

namespace App\Services;

use App\Models\Source;
use Illuminate\Support\Facades\Http;

class NewsAPIAdapter extends BaseNewsAPIAdapter
{
    public function fetchArticles(Source $source, string $category): ?array
    {
        $response = Http::get($source->base_url, [
            'apiKey' => $source->api_key,
            'country' => 'us',
            'category' => strtolower($category),
        ]);

        return $this->handleResponse($response);
    }

    protected function parseResponse($response): ?array
    {
        $articles = $response->json('articles');

        if (!is_array($articles)) {
            return null;
        }

        $parsedArticles = [];

        foreach ($articles as $newsItem) {
            $parsedArticles[] = [
                'title'        => $newsItem['title']       ?? null,
                'content'      => $newsItem['content']     ?? '',
                'author'       => $newsItem['author']      ?? null,
                'image_url'    => $newsItem['urlToImage']  ?? null,
                'published_at' => $newsItem['publishedAt'] ?? now(),
                'url'          => $newsItem['url']         ?? null,
            ];
        }

        return $parsedArticles;
    }

}