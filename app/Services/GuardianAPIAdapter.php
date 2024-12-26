<?php

namespace App\Services;

use App\Models\Source;
use Illuminate\Support\Facades\Http;

class GuardianAPIAdapter extends BaseNewsAPIAdapter
{
    public function fetchArticles(Source $source, string $category): ?array
    {
        $response = Http::get($source->base_url, [
            'api-key' => $source->api_key,
            'section' => strtolower($category),
            'show-fields' => 'all',
        ]);

        return $this->handleResponse($response);
    }

    protected function parseResponse($response): ?array
    {
        $results = $response->json('response.results');
        
        if (!$results) {
            return null;
        }

        $parsedArticles = [];
        foreach ($results as $item) {
            $parsedArticles[] = [
                'title'        => $item['fields']['headline'] ?? $item['webTitle'] ?? '',
                'content'      => $item['fields']['body'] ?? '',
                'author'       => $item['fields']['byline'] ?? '',
                'image_url'    => $item['fields']['thumbnail'] ?? null,
                'published_at' => $item['webPublicationDate'] ?? null,
                'url'          => $item['webUrl'] ?? null,
                'guardian_id'  => $item['id'] ?? null,
            ];
        }

        return $parsedArticles;
    }
    
}