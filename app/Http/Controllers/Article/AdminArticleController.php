<?php

namespace App\Http\Controllers\Article;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminArticleController extends Controller
{
    public function index()
    {
        $articles = Article::select('title', 'thumbnail', 'content')->get();

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

    public function update($id, Request $request)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event tidak ditemukan.'
            ], 404);
        }

        if ($event->user_id != Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki hak untuk mengedit event ini.'
            ], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'location' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'description' => 'required|string', // perbaikan dari 'text' jadi 'string'
        ]);

        $event->update([
            'title' => $request->title,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Event berhasil diperbarui.',
            'data' => $event,
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
