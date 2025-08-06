<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
        public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        Article::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'body' => $request->body,
            'status' => 'pending', // auto pending
        ]);

        return redirect()->back()->with('success', 'Artikel berhasil dikirim untuk ditinjau admin.');
    }

    public function create()
{
    return inertia('Articles/Create'); // Pastikan file Vue-nya ada ya
}

public function show(Article $article)
{
    abort_unless($article->status === 'published', 403);

    return Inertia::render('Articles/Show', [
        'article' => $article->load('user'),
    ]);
}

    }
