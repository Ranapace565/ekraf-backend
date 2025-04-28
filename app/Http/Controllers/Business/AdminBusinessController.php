<?php

namespace App\Http\Controllers\Business;

use App\Models\User;
use App\Models\Sector;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\BusinessSubmission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminBusinessController extends Controller
{
    public function index(Request $request)
    {

        $query = Business::query();
        // tak tambahi where

        // Filter berdasarkan sektor_id
        if ($request->has('sektor')) {
            $query->where('sector_id', $request->sektor);
        }

        if ($request->has('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        }

        // Search berdasarkan nama usaha
        if ($request->has('search')) {
            $query->where('business_name', 'like', '%' . $request->search . '%');
        }

        // Paginasi
        $perPage = $request->input('per_page', 10);
        $businesses = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'message' => 'Bisnis ditemukan',
            'data' => $businesses
        ]);
    }

    public function show($id)
    {

        $business = Business::where('id', $id)->get();

        if (!$business) {
            return response()->json([
                'message' => 'Usaha tidak ditemukan'
            ]);
        }

        return response()->json([
            'message' => 'Usaha ditemukan',
            'data' => $business,
        ], 201);
    }

    public function disable(Request $request, $id)
    {
        $business = Business::find($id);

        if (!$business) {
            return response()->json([
                'message' => 'Usaha tidak ditemukan.'
            ], 404);
        }

        if ($business->user_id != Auth::id() && Auth::user()->role != 'admin') {
            return response()->json([
                'message' => 'Anda tidak memiliki hak untuk menonaktifkan usaha ini.'
            ], 403);
        }

        $note = null;
        if (Auth::user()->role == 'admin') {
            $validated = $request->validate([
                'note' => 'nullable|string|max:255',
            ]);
            $note = $validated['note'] ?? null;
        }

        $business->status = 0;
        $business->note = $note;
        $business->save();

        return response()->json([
            'message' => 'Usaha berhasil dinonaktifkan.',
            'business' => $business
        ]);
    }


    public function activate($id)
    {
        $business = Business::find($id);

        if (!$business) {
            return response()->json([
                'message' => 'Usaha tidak ditemukan.'
            ], 404);
        }

        if ($business->user_id != Auth::id() && Auth::user()->role != 'admin') {
            return response()->json([
                'message' => 'Anda tidak memiliki hak untuk mengaktifkan usaha ini.'
            ], 403);
        }

        $business->status = 1;
        $business->save();

        return response()->json([
            'message' => 'Usaha berhasil diaktifkan.',
            'business' => $business
        ]);
    }

    public function destroy($id)
    {
        $business = Business::find($id);

        if (!$business) {
            return response()->json([
                'message' => 'Usaha tidak ditemukan.'
            ], 404);
        }

        if ($business->user_id != Auth::id() && !Auth::user()->is_admin) {
            return response()->json([
                'message' => 'Anda tidak memiliki hak untuk menghapus usaha ini.'
            ], 403);
        }

        $user = User::find($business->user_id);
        if ($user) {
            $user->role = 'visitor_logged';
            $user->save(); // Simpan perubahan
        }

        if ($business->profile) {
            $profilePath = storage_path('app/public/' . $business->profile);
            if (file_exists($profilePath)) {
                unlink($profilePath);
            }
        }

        $business->delete();

        return response()->json([
            'message' => 'Usaha dan perubahan role user berhasil.'
        ]);
    }
}
