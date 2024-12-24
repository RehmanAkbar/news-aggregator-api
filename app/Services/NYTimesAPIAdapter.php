<?php

namespace App\Services;

use App\Models\Source;
use Illuminate\Support\Facades\Http;

class NYTimesAPIAdapter extends BaseNewsAPIAdapter
{
    public function fetchArticles(Source $source, string $category): ?array
    {
        $response = Http::get(
            "{$source->base_url}/{$category}.json",
            ['api-key' => $source->api_key]
        );

        return $this->handleResponse($response);
    }

    protected function parseResponse($response): ?array
    {
        return $response->json('results');
    }
}