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
        $results = $response->json('results');
        if (!is_array($results)) {
            return null;
        }

        $parsedArticles = [];

        foreach ($results as $item) {
            $parsedArticles[] = [
                'title'        => $item['title']            ?? '',
                'content'      => $item['abstract']         ?? '',
                'author'       => $item['byline']           ?? '',
                'image_url'    => isset($item['multimedia'][0]['url'])
                                ? $item['multimedia'][0]['url']
                                : null,
                'published_at' => $item['published_date']   ?? now(),
                'url'          => $item['url']              ?? null,
            ];
        }

        return $parsedArticles;
    }

}