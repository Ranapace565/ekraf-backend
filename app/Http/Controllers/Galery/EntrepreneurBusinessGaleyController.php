<?php

namespace App\Http\Controllers\Galery;

use App\Models\Gallery;
use App\Models\Business;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EntrepreneurBusinessGaleyController extends Controller
{
    public function index($businessId)
    {
        $galleries = Gallery::where('business_id', $businessId)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($galleries->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada galeri untuk bisnis ini.'
            ], 404);
        }

        return response()->json([
            'message' => 'Daftar galeri untuk bisnis.',
            'data' => $galleries
        ], 200);
    }
    public function store(Request $request)
    {
        $userId = Auth::id();

        $business = Business::where('user_id', $userId)->first();

        if (!$business) {
            return response()->json([
                'message' => 'Bisnis tidak ditemukan untuk user ini.'
            ], 404);
        }

        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'caption' => 'required|string|max:255',
        ]);

        if ($request->hasFile('photo')) {
            $captionSlug = Str::slug($validated['caption']); // Nama file dari caption
            $extension = $request->file('photo')->getClientOriginalExtension();
            $filename = $captionSlug . '.' . $extension;

            // Cek apakah sudah ada file dengan nama yang sama, jika iya, tambahkan angka unik
            $counter = 1;
            $baseFilename = $captionSlug;
            while (Storage::disk('public')->exists('businesses/galleries/' . $filename)) {
                $filename = $baseFilename . '-' . $counter . '.' . $extension;
                $counter++;
            }

            $photoPath = $request->file('photo')->storeAs('businesses/galleries', $filename, 'public');
        } else {
            return response()->json([
                'message' => 'Foto wajib diunggah.'
            ], 422);
        }

        $gallery = new Gallery();
        $gallery->business_id = $business->id;
        $gallery->photo = $photoPath;
        $gallery->caption = $validated['caption'];
        $gallery->save();

        return response()->json([
            'message' => 'Gallery berhasil ditambahkan.',
            'data' => $gallery
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return response()->json([
                'message' => 'Galeri tidak ditemukan.'
            ], 404);
        }

        // Cek apakah user adalah pemilik business
        $business = Business::find($gallery->business_id);

        if (!$business || $business->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk mengupdate galeri ini.'
            ], 403);
        }

        $validated = $request->validate([
            'caption' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Update caption
        $gallery->caption = $validated['caption'];

        if ($request->hasFile('photo')) {
            // Hapus photo lama dari storage
            if ($gallery->photo && Storage::disk('public')->exists($gallery->photo)) {
                Storage::disk('public')->delete($gallery->photo);
            }

            $captionSlug = Str::slug($validated['caption']);
            $extension = $request->file('photo')->getClientOriginalExtension();
            $filename = $captionSlug . '.' . $extension;

            $counter = 1;
            $baseFilename = $captionSlug;
            while (Storage::disk('public')->exists('businesses/galleries/' . $filename)) {
                $filename = $baseFilename . '-' . $counter . '.' . $extension;
                $counter++;
            }

            $photoPath = $request->file('photo')->storeAs('businesses/galleries', $filename, 'public');
            $gallery->photo = $photoPath;
        }

        $gallery->save();

        return response()->json([
            'message' => 'Galeri berhasil diperbarui.',
            'data' => $gallery
        ], 200);
    }

    public function destroy($id)
    {
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return response()->json([
                'message' => 'Galeri tidak ditemukan.'
            ], 404);
        }

        // Cek apakah user adalah pemilik business
        $business = Business::find($gallery->business_id);

        if (!$business || $business->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk menghapus galeri ini.'
            ], 403);
        }

        // Hapus file foto dari storage kalau ada
        if ($gallery->photo && Storage::disk('public')->exists($gallery->photo)) {
            Storage::disk('public')->delete($gallery->photo);
        }

        // Hapus data galeri dari database
        $gallery->delete();

        return response()->json([
            'message' => 'Galeri berhasil dihapus.'
        ], 200);
    }
}
