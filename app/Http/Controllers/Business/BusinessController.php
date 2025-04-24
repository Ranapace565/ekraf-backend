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
        $query = Business::query();

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

        return response()->json($businesses);
    }
}
