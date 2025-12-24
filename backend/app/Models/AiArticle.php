<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AiArticle; 

class AiArticle extends Model
{
    protected $table = 'ai_articles';
    protected $fillable = [
        'article_id',
        'title',
        'content',
        'references',
        'model_used',
    ];
    protected $casts = [
        'references' => 'array',
    ];
    protected $hidden = ['article'];
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
