<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Models\BusinessSubmission;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class BusinessSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = BusinessSubmission::query();

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
    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'location' => 'required|string',
            'owner_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'sector_id' => 'required|int|max:255',
        ]);

        $submission = BusinessSubmission::create([
            'user_id' => Auth::id(),
            'business_name' => $request->business_name,
            'location' => $request->location,
            'owner_name' => $request->owner_name,
            'description' => $request->description,
            'sector_id' => $request->sector_id,
        ]);

        return response()->json([
            'message' => 'Pengajuan usaha berhasil dikirim.',
            'data' => $submission,
        ], 201);
    }
}
