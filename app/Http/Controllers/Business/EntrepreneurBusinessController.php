<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EntrepreneurBusinessController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $usaha = Business::where('user_id', $user->id);

        return response()->json([
            'message' => 'Data usaha berhasil ditemukan',
            'data' => $usaha
        ]);
    }

    public function update(Request $request, $id)
    {
        // $user = auth()->user();

        // $usaha = Business::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $usaha2 = Business::find($id);
        $usaha = Business::find($id);

        if (!$usaha) {
            return response()->json([
                'message' => 'Data usaha tidak ditemukan.'
            ], 404);
        }

        if ($usaha->user_id !== $user->id) {
            return response()->json([
                'message' => 'Akses ditolak. Anda tidak berhak mengubah data usaha ini.'
            ], 403);
        }

        $validated = $request->validate([
            'business_name' => 'sometimes|string|max:255',
            'owner_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'instagram' => 'nullable|string',
            'facebook' => 'nullable|string',
            'tiktok' => 'nullable|string',
        ]);

        $usaha->update($validated);

        return response()->json([
            'message' => 'Data usaha berhasil diperbarui',
            'data' => $usaha
        ]);
    }

    public function disable($id)
    {
        $user = auth()->user();
        $usaha = Business::where('user_id', $user->id);

        if (!$usaha) {
            return response()->json([
                'message' => 'Data usaha tidak ditemukan.'
            ], 404);
        }

        if ($usaha->user_id !== $user->id) {
            return response()->json([
                'message' => 'Akses ditolak. Anda tidak berhak menonaktifkan usaha ini.'
            ], 403);
        }

        $usaha->update(['status' => false]);

        return response()->json([
            'message' => 'Data usaha berhasil dinonaktifkan',
            'data' => $usaha
        ]);
    }

    public function activate($id)
    {
        $user = auth()->user();
        $usaha = Business::where('user_id', $user->id);

        if (!$usaha) {
            return response()->json([
                'message' => 'Data usaha tidak ditemukan.'
            ], 404);
        }

        if ($usaha->user_id !== $user->id) {
            return response()->json([
                'message' => 'Akses ditolak. Anda tidak berhak mengaktifkan usaha ini.'
            ], 403);
        }

        $usaha->update(['status' => true]);

        return response()->json([
            'message' => 'Data usaha berhasil dinonaktifkan',
            'data' => $usaha
        ]);
    }

    public function FindByUser($id)
    {
        return Business::where('user_id', $id);
    }
}
