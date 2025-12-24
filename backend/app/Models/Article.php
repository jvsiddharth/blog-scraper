<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AiArticle;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'source_url',
        'is_generated',
        'references',
    ];

    protected $casts = [
        'is_generated' => 'boolean',
        'references' => 'array',
    ];

    /**
     * One-to-one relationship with AI-generated article
     */
    public function ai()
    {
        return $this->hasOne(AiArticle::class, 'article_id');
    }
}
