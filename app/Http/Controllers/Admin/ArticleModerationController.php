<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleModerationController extends Controller
{
    public function index()
    {
        $articles = Article::with('user')->where('status', 'pending')->get();

        return Inertia::render('Admin/Articles/Index', [
            'articles' => $articles,
        ]);
    }

    public function approve(Article $article)
    {
        $article->update(['status' => 'published']);
        return back()->with('success', 'Artikel disetujui.');
    }

public function reject(Request $request, Article $article)
{
    $request->validate([
        'reason' => 'required|string|max:500',
    ]);

    $article->update([
        'status' => 'rejected',
        'reject_reason' => $request->reason,
    ]);

    return back()->with('success', 'Artikel ditolak dengan alasan.');
}

}
