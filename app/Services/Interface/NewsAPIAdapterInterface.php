<?php

namespace App\Services\Interface;

use App\Models\Source;

interface NewsAPIAdapterInterface
{
    public function fetchArticles(Source $source, string $category): ?array;
}