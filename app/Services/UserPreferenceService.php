<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPreference;

class UserPreferenceService
{
    /**
     * Get user preferences
     *
     * @param User $user
     * @return UserPreference|null
     */
    public function getUserPreferences(User $user): ?UserPreference
    {
        return $user->preferences;
    }

    /**
     * Update user preferences
     *
     * @param User $user
     * @param array $preferences
     * @return UserPreference
     */
    public function updatePreferences(User $user, array $preferences): UserPreference
    {
        return $user->preferences()->updateOrCreate(
            ['user_id' => $user->id],
            $this->sanitizePreferences($preferences)
        );
    }

    /**
     * Sanitize and validate preference data
     *
     * @param array $preferences
     * @return array
     */
    private function sanitizePreferences(array $preferences): array
    {
        return [
            'preferred_categories' => array_filter($preferences['preferred_categories'] ?? []),
            'preferred_sources' => array_filter($preferences['preferred_sources'] ?? []),
            'preferred_authors' => array_filter($preferences['preferred_authors'] ?? []),
        ];
    }
}