<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\Interface\NewsAPIAdapterInterface;

abstract class BaseNewsAPIAdapter implements NewsAPIAdapterInterface
{
    protected function handleResponse($response): ?array
    {
        if (!$response->successful()) {
            Log::error("API request failed: " . $response->body());
            return null;
        }

        return $this->parseResponse($response);
    }

    abstract protected function parseResponse($response): ?array;
}