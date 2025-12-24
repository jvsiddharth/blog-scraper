<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\AiArticle;
use Illuminate\Http\Request;

class AiArticleController extends Controller
{
    /**
     * Called by Node worker to upsert AI-generated content
     */
    public function upsert(Request $request, Article $article)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'references' => 'nullable|array',
            'model_used' => 'nullable|string',
        ]);

        $ai = AiArticle::updateOrCreate(
            ['article_id' => $article->id],
            [
                'title' => $data['title'],
                'content' => $data['content'],
                'references' => $data['references'] ?? [],
                'model_used' => $data['model_used'] ?? null,
            ]
        );

        return response()->json($ai);
    }
}

