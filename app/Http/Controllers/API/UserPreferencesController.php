<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use App\Services\ArticleHelperService;
use App\Http\Controllers\ApiController;
use App\Http\Resources\ArticleResource;
use App\Services\UserPreferenceService;
use App\Http\Resources\UserPreferenceResource;
use App\Http\Requests\UpdateUserPreferencesRequest;

class UserPreferencesController extends ApiController
{
    public function __construct(
        private readonly UserPreferenceService $preferenceService,
        private readonly ArticleHelperService $articleService
    ) {}

    /**
     * Get user's preferences
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $preferences = $this->preferenceService->getUserPreferences(
            auth()->user()
        );

        return $this->successResponse(
            new UserPreferenceResource($preferences),
            'User preferences retrieved successfully'
        );
    }

    /**
     * Update user's preferences
     *
     * @param UpdateUserPreferencesRequest $request
     * @return JsonResponse
     */
    public function update(UpdateUserPreferencesRequest $request): JsonResponse
    {
        $preferences = $this->preferenceService->updatePreferences(
            auth()->user(),
            $request->validated()
        );

        return $this->successResponse(
            new UserPreferenceResource($preferences),
            'User preferences updated successfully'
        );
    }

    /**
     * Get personalized news feed based on user preferences
     *
     * @return JsonResponse
     */
    public function personalizedFeed(): JsonResponse
    {
        $user = auth()->user();
        $preferences = $this->preferenceService->getUserPreferences($user);

        if (!$preferences) {
            return $this->successResponse(
                ArticleResource::collection(
                    $this->articleService->getArticles([])
                ),
                'No preferences set, showing general feed'
            );
        }

        $articles = $this->articleService->getPersonalizedFeed(
            $preferences->toArray()
        );

        return $this->successResponse(
            ArticleResource::collection($articles),
            'Personalized feed retrieved successfully'
        );
    }
}