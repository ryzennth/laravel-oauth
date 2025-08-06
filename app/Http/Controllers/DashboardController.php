<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
{
    $articles = Article::where('status', 'published')
        ->latest()
        ->with('user')
        ->take(10)
        ->get();

    return Inertia::render('Dashboard', [
        'articles' => $articles,
    ]);
}
}
