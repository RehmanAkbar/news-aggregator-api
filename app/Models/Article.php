<?php

namespace App\Models;

use App\Models\Source;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'title',
        'content',
        'author',
        'url',
        'image_url',
        'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_bookmarks')
            ->withTimestamps();
    }
}
