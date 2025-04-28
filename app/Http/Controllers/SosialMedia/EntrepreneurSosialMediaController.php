<?php

namespace App\Http\Controllers\SosialMedia;

use App\Models\Business;
use App\Models\SosialMedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EntrepreneurSosialMediaController extends Controller
{

    public function indexByUser()
    {
        $userId = Auth::id();

        $business = Business::where('user_id', $userId)->first();

        if (!$business) {
            return response()->json([
                'message' => 'Bisnis tidak ditemukan untuk user ini.'
            ], 404);
        }

        $sosialMedias = SosialMedia::where('business_id', $business->id)->get();

        if ($sosialMedias->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada sosial media untuk bisnis ini.'
            ], 404);
        }

        return response()->json([
            'message' => 'Daftar sosial media berdasarkan user.',
            'data' => $sosialMedias,
        ], 200);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'caption' => 'required|string|max:255',
            'uri' => 'required|url|max:255',
            'type' => 'required|string|max:50',
        ]);

        $userId = Auth::id();

        $business = Business::where('user_id', $userId)->first();

        if (!$business) {
            return response()->json([
                'message' => 'Bisnis tidak ditemukan untuk user ini.'
            ], 404);
        }

        $sosialMedia = new SosialMedia();
        $sosialMedia->user_id = $userId;
        $sosialMedia->business_id = $business->id; // otomatis ambil business_id
        $sosialMedia->caption = $validated['caption'];
        $sosialMedia->uri = $validated['uri'];
        $sosialMedia->type = $validated['type'];
        $sosialMedia->save();

        return response()->json([
            'message' => 'Sosial media berhasil ditambahkan.',
            'data' => $sosialMedia,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();

        $sosialMedia = SosialMedia::find($id);

        if (!$sosialMedia) {
            return response()->json([
                'message' => 'Sosial media tidak ditemukan.'
            ], 404);
        }

        $business = Business::where('user_id', $userId)->first();

        if (!$business || $sosialMedia->business_id !== $business->id) {
            return response()->json([
                'message' => 'Anda tidak memiliki hak untuk mengedit sosial media ini.'
            ], 403);
        }

        // Validasi input
        $validated = $request->validate([
            'caption' => 'required|string|max:255',
            'uri' => 'required|url|max:255',
            'type' => 'required|string|max:50',
        ]);

        // Update data
        $sosialMedia->caption = $validated['caption'];
        $sosialMedia->uri = $validated['uri'];
        $sosialMedia->type = $validated['type'];

        $sosialMedia->save();

        return response()->json([
            'message' => 'Sosial media berhasil diperbarui.',
            'data' => $sosialMedia
        ], 200);
    }
    public function destroy($id)
    {
        $userId = Auth::id();

        // Cari sosial media berdasarkan ID
        $sosialMedia = SosialMedia::find($id);

        if (!$sosialMedia) {
            return response()->json([
                'message' => 'Sosial media tidak ditemukan.'
            ], 404);
        }

        // Cari bisnis milik user yang sedang login
        $business = Business::where('user_id', $userId)->first();

        if (!$business || $sosialMedia->business_id !== $business->id) {
            return response()->json([
                'message' => 'Anda tidak memiliki hak untuk menghapus sosial media ini.'
            ], 403);
        }

        // Hapus sosial media
        $sosialMedia->delete();

        return response()->json([
            'message' => 'Sosial media berhasil dihapus.'
        ], 200);
    }
}
