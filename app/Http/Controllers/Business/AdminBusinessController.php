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

    public function disable($id)
    {
        $usaha = Business::find($id);

        if (!$usaha) {
            return response()->json([
                'message' => 'Data usaha tidak ditemukan.'
            ], 404);
        }

        $usaha->update(['is_approved' => false]);

        return response()->json([
            'message' => 'Data usaha berhasil dinonaktifkan',
            'data' => $usaha
        ]);
    }
    public function activate($id)
    {
        $usaha = Business::find($id);

        if (!$usaha) {
            return response()->json([
                'message' => 'Data usaha tidak ditemukan.'
            ], 404);
        }

        $usaha->update(['is_approved' => true]);

        return response()->json([
            'message' => 'Data usaha berhasil dinonaktifkan',
            'data' => $usaha
        ]);
    }

    public function destroy($id)
    {
        $business = Business::find($id);

        if (!$business) {
            return response()->json([
                'message' => 'Data usaha tidak ditemukan.'
            ], 404);
        }

        $business->delete();

        return response()->json([
            'message' => 'Usaha berhasil dihapus.'
        ]);
    }
}
