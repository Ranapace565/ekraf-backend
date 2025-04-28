<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $query = Business::select('id', 'business_name', 'latitude', 'longitude', 'owner_name', 'profile');

        if ($request->has('sector')) {
            $query->where('sector_id', $request->sector);
        }

        if ($request->has('search')) {
            $query->where('business_name', 'like', '%' . $request->search . '%');
        }

        // status
        $query->where('status', 1)->where('active', true);

        $perPage = $request->input('per_page', 10);

        $businesses = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($businesses);
    }

    public function show($id)
    {
        $business = Business::where('status', 1)->where('active', true)->find($id);

        if (!$business) {
            return response()->json([
                'message' => 'Usaha tidak ditemukan atau tidak aktif.'
            ], 404);
        }

        return response()->json($business);
    }
}
