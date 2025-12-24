<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AiArticleController;

Route::get('/health', function () {
    DB::connection()->getPdo();

    return response()->json([
        'status' => 'ok',
        'db' => 'pgsql connected'
    ]);
});

Route::post(
    '/articles/{article}/ai',
    [AiArticleController::class, 'upsert']
);

Route::apiResource('articles', ArticleController::class);
