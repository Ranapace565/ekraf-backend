<?php

namespace App\Http\Controllers\Galery;

use App\Models\Gallery;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EntrepreneurBusinessGaleyController extends Controller
{
    public function uploadProof(Request $request)
    {
        $request->validate([
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'caption' => 'nullable|string',
        ]);

        $user = Auth::user(); // user dari token

        // Ambil business milik user ini
        $business = Business::where('user_id', $user->id)->first();

        if (!$business) {
            return response()->json([
                'message' => 'Usaha belum didaftarkan untuk user ini.'
            ], 404);
        }

        // Simpan file
        $path = $request->file('proof')->store('uploads/proofs', 'public');

        // Simpan ke database
        $gallery = Gallery::create([
            'business_id' => $business->id,
            'photo' => $path,
            'caption' => $request->caption,
        ]);

        return response()->json([
            'message' => 'Upload berhasil',
            'data' => $gallery
        ]);
    }
}
