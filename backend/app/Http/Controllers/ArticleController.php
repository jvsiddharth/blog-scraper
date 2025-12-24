<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        return Article::latest()->get();
    }

    public function show(Article $article)
    {
        $article->load('ai');

        return response()->json([
            'id' => $article->id,
            'title' => $article->title,
            'content' => $article->content,
            'source_url' => $article->source_url,
            'created_at' => $article->created_at,
            'ai' => $article->ai ? [
                'id' => $article->ai->id,
                'title' => $article->ai->title,
                'content' => $article->ai->content,
                'references' => $article->ai->references,
                'model_used' => $article->ai->model_used,
            ] : null,
        ]);
    }


    public function store(Request $request)
    {
        return Article::create($request->all());
    }

    public function update(Request $request, Article $article)
    {
        $article->update($request->all());
        return $article;
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return response()->noContent();
    }
}

