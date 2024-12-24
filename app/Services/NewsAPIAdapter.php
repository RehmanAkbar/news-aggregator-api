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
        return $response->json('articles');
    }
}