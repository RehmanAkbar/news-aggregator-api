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
        return $response->json('response.results');
    }
}