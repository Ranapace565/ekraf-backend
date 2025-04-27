<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BusinessController extends Controller
{
    // public function index()
    // {
    //     $businesses = Business::with(['user', 'sector'])->get();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'List semua data usaha',
    //         'data' => $businesses
    //     ]);
    // }

    public function index(Request $request)
    {
        $query = Business::query()->where('is_approved', true);
        // tak tambahi where

        // Filter berdasarkan sektor_id
        if ($request->has('sektor')) {
            $query->where('sector_id', $request->sektor);
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
        $business = Business::where('id', $id)
            ->where('is_approved', true)
            ->first();

        if (!$business) {
            return response()->json([
                'message' => 'Bisnis tidak ditemukan atau belum disetujui'
            ], 404);
        }

        return response()->json([
            'message' => 'Bisnis ditemukan',
            'data' => $business
        ], 200);
    }
}
