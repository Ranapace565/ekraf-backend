<?php

namespace App\Http\Controllers\SosialMedia;

use App\Models\User;
use App\Models\Business;
use App\Models\SosialMedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminSosialMediaController extends Controller
{

    // public function index()
    // {
    //     // Ambil id user yang sedang login
    //     $userId = Auth::id();

    //     // Ambil data user yang sedang login
    //     $user = Auth::user();

    //     if ($user->role === 'admin') {
    //         // Tampilkan semua sosial media yang dibuat oleh user dengan role admin
    //         $sosialMedias = SosialMedia::whereHas('user', function ($query) {
    //             $query->where('role', 'admin');
    //         })->get();

    //         if ($sosialMedias->isEmpty()) {
    //             return response()->json([
    //                 'message' => 'Belum ada sosial media untuk admin.'
    //             ], 404);
    //         }

    //         return response()->json([
    //             'message' => 'Daftar sosial media milik admin.',
    //             'data' => $sosialMedias,
    //         ], 200);
    //     }

    //     $sosialMedias = SosialMedia::where('user_id', $userId)->get();

    //     if ($sosialMedias->isEmpty()) {
    //         return response()->json([
    //             'message' => 'Belum ada sosial media untuk dinas ini.'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'message' => 'Daftar sosial media berdasarkan user.',
    //         'data' => $sosialMedias,
    //     ], 200);
    // }


    public function index()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Cek apakah user adalah admin
        if ($user->role === 'admin') {
            // Admin dapat melihat semua sosial media yang dimiliki oleh admin lain
            $sosialMedias = SosialMedia::whereHas('user', function ($query) {
                $query->where('role', 'admin'); // Hanya sosial media yang dimiliki oleh admin
            })->get();

            if ($sosialMedias->isEmpty()) {
                return response()->json([
                    'message' => 'Belum ada sosial media untuk admin.'
                ], 404);
            }

            return response()->json([
                'message' => 'Daftar sosial media milik admin.',
                'data' => $sosialMedias,
            ], 200);
        }

        // Jika user adalah user biasa, hanya tampilkan sosial media yang milik user tersebut
        $sosialMedias = SosialMedia::where('user_id', $user->id)->get();

        if ($sosialMedias->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada sosial media untuk user ini.'
            ], 404);
        }

        return response()->json([
            'message' => 'Daftar sosial media milik user.',
            'data' => $sosialMedias,
        ], 200);
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'caption' => 'required|string|max:255',
    //         'uri' => 'required|url|max:255',
    //         'type' => 'required|string|max:50',
    //     ]);

    //     $userId = Auth::id();

    //     $sosialMedia = new SosialMedia();
    //     $sosialMedia->user_id = $userId;
    //     $sosialMedia->caption = $validated['caption'];
    //     $sosialMedia->uri = $validated['uri'];
    //     $sosialMedia->type = $validated['type'];
    //     $sosialMedia->save();

    //     return response()->json([
    //         'message' => 'Sosial media berhasil ditambahkan.',
    //         'data' => $sosialMedia,
    //     ], 201);
    // }

    // public function update(Request $request, $id)
    // {

    //     $sosialMedia = SosialMedia::find($id);

    //     if (!$sosialMedia) {
    //         return response()->json([
    //             'message' => 'Sosial media tidak ditemukan.'
    //         ], 404);
    //     }

    //     $sosialRole = User::where($id, $sosialMedia->user_id);

    //     if ($sosialRole->role !== 'admin') {
    //         return response()->json([
    //             'message' => 'Sosial media ini bukan milik admin.'
    //         ], 403);
    //     }

    //     $validated = $request->validate([
    //         'caption' => 'required|string|max:255',
    //         'uri' => 'required|url|max:255',
    //         'type' => 'required|string|max:50',
    //     ]);

    //     $sosialMedia->caption = $validated['caption'];
    //     $sosialMedia->uri = $validated['uri'];
    //     $sosialMedia->type = $validated['type'];

    //     $sosialMedia->save();

    //     return response()->json([
    //         'message' => 'Sosial media berhasil diperbarui.',
    //         'data' => $sosialMedia
    //     ], 200);
    // }


    public function store(Request $request)
    {
        // Validasi input dari request
        $validated = $request->validate([
            'caption' => 'required|string|max:255',
            'uri' => 'required|url|max:255',
            'type' => 'required|string|max:50',
        ]);

        // Ambil ID user yang sedang login
        $userId = Auth::id();

        // Cek apakah user adalah admin
        $user = Auth::user();

        // Jika admin, biarkan business_id kosong
        if ($user->role === 'admin') {
            $businessId = null;  // Admin tidak perlu business_id
        } else {
            // Jika bukan admin, cek apakah user memiliki business
            $business = Business::where('user_id', $userId)->first();

            if (!$business) {
                return response()->json([
                    'message' => 'Bisnis tidak ditemukan untuk user ini.'
                ], 404);
            }

            // Jika bukan admin, ambil business_id dari bisnis yang dimiliki oleh user
            $businessId = $business->id;
        }

        // Buat sosial media baru
        $sosialMedia = new SosialMedia();
        $sosialMedia->user_id = $userId;
        $sosialMedia->business_id = $businessId;  // Menentukan business_id sesuai role
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

        $user = $sosialMedia->user;

        if ($user->role === 'admin') {
            // Admin dapat memperbarui sosial media yang dimiliki oleh admin lain
            $sosialMedia->caption = $request->caption ?? $sosialMedia->caption;
            $sosialMedia->uri = $request->uri ?? $sosialMedia->uri;
            $sosialMedia->type = $request->type ?? $sosialMedia->type;
        } elseif ($userId !== $user->id) {
            // User biasa hanya dapat memperbarui sosial media miliknya sendiri
            return response()->json([
                'message' => 'Anda tidak memiliki hak untuk memperbarui sosial media ini.'
            ], 403);
        } else {
            // Jika user adalah pemilik sosial media (bukan admin), maka bisa memperbarui sosial medianya
            $validated = $request->validate([
                'caption' => 'required|string|max:255',
                'uri' => 'required|url|max:255',
                'type' => 'required|string|max:50',
            ]);

            $sosialMedia->caption = $validated['caption'];
            $sosialMedia->uri = $validated['uri'];
            $sosialMedia->type = $validated['type'];
        }

        // Simpan perubahan ke database
        $sosialMedia->save();

        return response()->json([
            'message' => 'Sosial media berhasil diperbarui.',
            'data' => $sosialMedia
        ], 200);
    }


    // public function destroy($id)
    // {
    //     $sosialMedia = SosialMedia::find($id);

    //     if (!$sosialMedia) {
    //         return response()->json([
    //             'message' => 'Sosial media tidak ditemukan.'
    //         ], 404);
    //     }

    //     $sosialRole = User::where($id, $sosialMedia->user_id);

    //     if ($sosialRole->role !== 'admin') {
    //         return response()->json([
    //             'message' => 'Sosial media ini bukan milik admin.'
    //         ], 403);
    //     }

    //     // Hapus sosial media
    //     $sosialMedia->delete();

    //     return response()->json([
    //         'message' => 'Sosial media berhasil dihapus.'
    //     ], 200);
    // }

    public function destroy($id)
    {
        // Ambil id user yang sedang login
        $userId = Auth::id();

        // Cari sosial media berdasarkan ID
        $sosialMedia = SosialMedia::find($id);

        if (!$sosialMedia) {
            return response()->json([
                'message' => 'Sosial media tidak ditemukan.'
            ], 404);
        }

        // Ambil data user yang memiliki sosial media ini
        $user = $sosialMedia->user;

        // Cek apakah user yang login adalah admin atau pemilik sosial media
        if ($user->role === 'admin') {
            // Admin dapat menghapus sosial media milik admin lain
            $sosialMedia->delete();
            return response()->json([
                'message' => 'Sosial media berhasil dihapus.'
            ], 200);
        } elseif ($userId !== $user->id) {
            // User biasa hanya dapat menghapus sosial media miliknya sendiri
            return response()->json([
                'message' => 'Anda tidak memiliki hak untuk menghapus sosial media ini.'
            ], 403);
        } else {
            // Jika user adalah pemilik sosial media (bukan admin), maka bisa menghapus sosial media miliknya sendiri
            $sosialMedia->delete();
            return response()->json([
                'message' => 'Sosial media berhasil dihapus.'
            ], 200);
        }
    }
}
