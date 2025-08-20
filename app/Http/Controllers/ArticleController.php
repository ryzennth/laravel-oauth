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
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'body' => 'required|string',
        'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('cover_image')) {
        $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
    }

    Article::create([
        'user_id' => Auth::id(),    
        'title' => $validated['title'],
        'body' => $validated['body'],
        'cover_image' => $validated['cover_image'] ?? null,
        'status' => 'pending',
    ]);

    return redirect()->back()->with('success', 'Artikel berhasil dikirim untuk ditinjau admin.');
}



    public function create()
{
    return Inertia::render("Admin/Articles/Create",[

    ]);
}

public function show(Article $article)
{

    $articles = Article::with('user')->latest()->get()->append('cover_image_url');

    abort_unless($article->status === 'published', 403);

    return Inertia::render('Articles/Show', [
        'article' => $article->load('user'),
    ]);
}

    public function index()
    {
        $articles = Article::with('user')->latest()->get()->append('cover_image_url');

        return Inertia::render('Articles/Index');
    }
    

    public function listPenulis()
    {
        $articles = Article::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return Inertia::render('Author/Articles/Index', [
            'articles' => $articles,
        ]);
    }

public function edit(Article $article)
{
    abort_if($article->user_id !== auth()->id(), 403);

    return Inertia::render('Author/Articles/Edit', [
        'article' => $article
    ]);
}

public function update(Request $request, Article $article)
{
    abort_if($article->user_id !== auth()->id(), 403);
    abort_if($article->status === 'published', 403);

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'body' => 'required|string',
        'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('cover_image')) {
        // hapus cover lama kalau ada
        if ($article->cover_image && \Storage::disk('public')->exists($article->cover_image)) {
            \Storage::disk('public')->delete($article->cover_image);
        }

        $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
    }

    $article->update([
        'title' => $validated['title'],
        'body' => $validated['body'],
        'cover_image' => $validated['cover_image'] ?? $article->cover_image,
        'status' => 'pending',
        'reject_reason' => null
    ]);

    return redirect()->route('author.articles.index')
        ->with('success', 'Artikel berhasil diperbarui.');
}



    // 🔹 PENULIS – resubmit artikel rejected
    public function resubmit(Article $article)
    {
        $this->authorize('update', $article);
        $article->update([
            'status' => 'pending',
            'reject_reason' => null,
        ]);

        return back()->with('success', 'Artikel berhasil diajukan ulang.');
    }

    // 🔹 ADMIN – daftar artikel pending/rejected
    public function listAdmin()
    {
        $articles = Article::whereIn('status', ['pending', 'rejected'])
            ->with('user')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Articles/Index', [
            'articles' => $articles,
        ]);
    }

    // 🔹 ADMIN – approve artikel
    public function approve(Article $article)
    {
        $this->authorize('approve', $article);
        $article->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'reject_reason' => null,
        ]);

        return back()->with('success', 'Artikel disetujui.');
    }

    // 🔹 ADMIN – reject artikel dengan alasan
    public function reject(Request $request, Article $article)
    {
        $this->authorize('approve', $article);
        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $article->update([
            'status' => 'rejected',
            'reject_reason' => $data['reason'],
        ]);

        return back()->with('success', 'Artikel ditolak.');
    }

    public function destroy(Article $article)
{
    // pastikan hanya pemilik artikel yang bisa hapus
    if ($article->user_id !== auth()->id()) {
        abort(403, 'Unauthorized action.');
    }

    // hapus cover image kalau ada
    if ($article->cover_image && \Storage::disk('public')->exists($article->cover_image)) {
        \Storage::disk('public')->delete($article->cover_image);
    }

    $article->delete();

    return redirect()->route('author.articles.index')
        ->with('success', 'Artikel berhasil dihapus.');
}


    }
