<?php

namespace App\Http\Controllers\Article;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all();

        $formattedArticles = $articles->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'thumbnail_url' => $article->thumbnail ? asset('storage/' . $article->thumbnail) : null,
                'short_description' => Str::limit(strip_tags($article->content), 100),
            ];
        });

        return response()->json([
            'articles' => $formattedArticles
        ]);
    }

    public function show($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article not found.'
            ], 404);
        }

        return response()->json([
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content,
            'thumbnail_url' => $article->thumbnail ? asset('storage/' . $article->thumbnail) : null,
            'expires_at' => $article->expires_at,
            'created_at' => $article->created_at,
            'updated_at' => $article->updated_at,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'expires_at' => 'nullable|date',
        ]);


        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            $titleSlug = Str::slug($request->input('title')); // buat slug dari judul
            $extension = $request->file('thumbnail')->getClientOriginalExtension(); // ambil ekstensi file asli
            $filename = $titleSlug . '.' . $extension; // gabungkan jadi nama file baru

            $thumbnailPath = $request->file('thumbnail')->storeAs(
                'article/thumbnails', // folder
                $filename,            // nama file
                'public'              // disk storage (public)
            );
        }

        $article = Article::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            // 'slug' => Str::slug($validated['title']) . '-' . uniqid(),
            'content' => $validated['content'],
            'thumbnail' => $thumbnailPath,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return response()->json([
            'message' => 'Article created successfully',
            'article' => $article
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article not found.'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'expires_at' => 'nullable|date',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($article->thumbnail) {
                $oldThumbnailPath = storage_path('app/public/' . $article->thumbnail);
                if (file_exists($oldThumbnailPath)) {
                    unlink($oldThumbnailPath);  // Hapus file thumbnail lama
                }
            }

            $titleSlug = Str::slug($request->input('title'));
            $extension = $request->file('thumbnail')->getClientOriginalExtension();
            $filename = $titleSlug . '.' . $extension;

            $thumbnailPath = $request->file('thumbnail')->storeAs('article/thumbnails', $filename, 'public');
            $article->thumbnail = $thumbnailPath;
        }

        $article->title = $validated['title'];
        // $article->slug = Str::slug($validated['title']); // Optional, regenerate slug
        $article->content = $validated['content'];
        $article->expires_at = $validated['expires_at'] ?? null;

        $article->save();

        return response()->json([
            'message' => 'Article updated successfully',
            'article' => $article
        ]);
    }
    public function destroy($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article not found.'
            ], 404);
        }

        if ($article->thumbnail) {
            Storage::disk('public')->delete($article->thumbnail);
        }

        $article->delete();

        return response()->json([
            'message' => 'Article deleted successfully'
        ], 200);
    }
}


// contoh upload
// const formData = new FormData();
// formData.append('title', 'Judul Artikel');
// formData.append('content', 'Isi artikel...');
// formData.append('thumbnail', selectedFile); // selectedFile = file yang dipilih user
// formData.append('expires_at', '2025-12-31'); // opsional

// axios.post('http://your-backend.com/api/articles', formData, {
//     headers: {
//         'Authorization': 'Bearer YOUR_ACCESS_TOKEN', // kalau pakai auth
//         'Content-Type': 'multipart/form-data',
//     }
// })
// .then(response => {
//     console.log(response.data);
// })
// .catch(error => {
//     console.error(error);
// });
