<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class BusinessGalleryController extends Controller
{
    public function uploadProof(Request $request)
    {
        $request->validate([
            // 'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
            'business_id' => 'required|exists:businesses,id',
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'caption' => 'nullable|string'
        ]);

        $path = $request->file('proof')->store('uploads/galeries_business', 'public');

        $gallery = Gallery::create([
            'business_id' => $request->business_id,
            'photo' => $path,
            'caption' => $request->caption
        ]);

        return response()->json([
            'message' => 'Upload berhasil',
            'path' => $path
        ]);
    }
}
