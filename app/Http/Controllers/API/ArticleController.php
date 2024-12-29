<?php

namespace App\Http\Controllers\API;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use App\Services\ArticleHelperService;
use App\Http\Controllers\ApiController;
use App\Http\Resources\ArticleResource;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\ArticleSearchRequest;

class ArticleController extends ApiController
{
    
      public function __construct(
        private readonly ArticleHelperService $articleService
    ) {}

    /**
     * Get paginated list of articles with optional filters
     *
     * @param ArticleSearchRequest $request
     * @return JsonResponse
     */
    public function index(ArticleSearchRequest $request): JsonResponse
    {
        $articles = $this->articleService->getArticles(
            $request->validated()
        );

        return $this->successResponse(
            ArticleResource::collection($articles),
            'Articles retrieved successfully'
        );
    }

    /**
     * Get a specific article by ID
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function show(Article $article): JsonResponse
    {
        $article = $this->articleService->getArticle($article);
        
        return $this->successResponse(
            new ArticleResource($article),
            'Article retrieved successfully'
        );
    }

}