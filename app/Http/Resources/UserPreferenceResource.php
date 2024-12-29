<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'preferred_categories' => $this->preferred_categories,
            'preferred_sources' => $this->preferred_sources,
            'preferred_authors' => $this->preferred_authors,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
