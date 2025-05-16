<?php

namespace App\Http\Controllers\Business;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\BusinessSubmission;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BusinessSubmissionController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query = BusinessSubmission::query();

    //     if ($request->has('sektor')) {
    //         $query->where('sector_id', $request->sektor);
    //     }

    //     if ($request->has('search')) {
    //         $query->where('business_name', 'like', '%' . $request->search . '%');
    //     }

    //     $perPage = $request->input('per_page', 10);
    //     $businesses = $query->orderBy('created_at', 'desc')->paginate($perPage);

    //     return response()->json($businesses);
    // }

    public function index(Request $request)
    {
        // $userId = Auth::id();

        $submissions = BusinessSubmission::where('user_id', Auth::id())
            ->select('id', 'business_name', 'description', 'proof')
            ->get();

        $formattedSubmissions = $submissions->map(function ($submission) {
            return [
                'id' => $submission->id,
                'business_name' => $submission->business_name,
                'short_description' => Str::limit(strip_tags($submission->description), 100), // Deskripsi singkat
                'proof_url' => $submission->proof ? asset('storage/' . $submission->proof) : null, // URL lengkap untuk foto bukti usaha
            ];
        });

        return response()->json([
            'submissions' => $formattedSubmissions
        ]);
    }


    public function show($id)
    {
        $submission = BusinessSubmission::find($id);

        if (!$submission) {
            return response()->json([
                'message' => 'Pengajuan usaha tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'submission' => [
                'id' => $submission->id,
                'user_id' => $submission->user_id,
                'business_name' => $submission->business_name,
                'owner_name' => $submission->owner_name,
                'description' => $submission->description,
                'location' => $submission->location,
                'latitude' => $submission->latitude,
                'longitude' => $submission->longitude,
                'proof_url' => $submission->proof ? asset('storage/' . $submission->proof) : null,  // URL lengkap untuk foto bukti usaha
                'note' => $submission->note,
                'sector_id' => $submission->sector_id,
                'created_at' => $submission->created_at,
                'updated_at' => $submission->updated_at,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $existing = BusinessSubmission::where('user_id', Auth::id())->first();
        if ($existing) {
            return response()->json([
                'message' => 'Kamu sudah mengajukan usaha. Setiap pengguna hanya boleh mengajukan satu usaha.'
            ], 400);
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'note' => 'nullable|string|max:255',
            'sector_id' => 'required|exists:sectors,id',
        ]);

        $proofPath = null;

        if ($request->hasFile('proof')) {
            $businessSlug = Str::slug($request->input('business_name'));
            $extension = $request->file('proof')->getClientOriginalExtension();
            $filename = $businessSlug . '.' . $extension;

            $proofPath = $request->file('proof')->storeAs(
                'business_submissions/proof', // folder di storage/app/public/
                $filename,
                'public'
            );
        }

        $submission = BusinessSubmission::create([
            'user_id' => Auth::id(),
            'business_name' => $validated['business_name'],
            'owner_name' => $validated['owner_name'],
            'description' => $validated['description'],
            'location' => $validated['location'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'proof' => $proofPath,
            'sector_id' => $validated['sector_id'] ?? null,
        ]);

        return response()->json([
            'message' => 'Pengajuan usaha berhasil dikirim.',
            'data' => $submission,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $submission = BusinessSubmission::find($id);

        if (!$submission) {
            return response()->json([
                'message' => 'Pengajuan usaha tidak ditemukan'
            ], 404);
        }

        if ($submission->user_id != Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki hak untuk mengedit pengajuan usaha ini.'
            ], 403);
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'note' => 'nullable|string|max:255',
            'sector_id' => 'nullable|exists:sectors,id'
        ]);

        $proofPath = $submission->proof;
        if ($request->hasFile('proof')) {
            if ($proofPath && Storage::exists('public/' . $proofPath)) {
                Storage::delete('public/' . $proofPath);
            }

            // Upload foto bukti baru
            $businessSlug = Str::slug($request->input('business_name'));
            $extension = $request->file('proof')->getClientOriginalExtension();
            $filename = $businessSlug . '.' . $extension;

            $proofPath = $request->file('proof')->storeAs(
                'business_submissions/proof', // folder di storage/app/public/
                $filename,
                'public'
            );
        }

        $submission->update([
            'business_name' => $validated['business_name'],
            'owner_name' => $validated['owner_name'],
            'description' => $validated['description'],
            'location' => $validated['location'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'proof' => $proofPath,
            'sector_id' => $validated['sector_id'] ?? null,
            'status' => true,
        ]);

        return response()->json([
            'message' => 'Update pengajuan usaha berhasil',
            'submission' => $submission
        ]);
    }
}
