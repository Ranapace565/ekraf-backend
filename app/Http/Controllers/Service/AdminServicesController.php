<?php

namespace App\Http\Controllers\Service;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminServicesController extends Controller
{
    public function index()
    {
        $services = Service::latest()->get();

        return response()->json([
            'message' => 'List of services',
            'data' => $services
        ], 200);
    }
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized. Hanya admin yang dapat membuat data Dinas.'
            ], 403);
        }

        $existingService = Service::where('user_id', $user->id)->first();
        if ($existingService) {
            return response()->json([
                'message' => 'Data dinas telah ada. Tidak dapat menambah lagi.'
            ], 409); // 409 Conflict
        }

        $validated = $request->validate([
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Profile sekarang wajib image
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
        ]);

        $profilePath = null;

        if ($request->hasFile('profile')) {
            $profilePath = $request->file('profile')->store(
                'services/profiles', // Folder dalam storage/app/public
                'public'
            );
        }

        $service = Service::create([
            'user_id' => $user->id,
            'profile' => $profilePath,
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        return response()->json([
            'message' => 'Service created successfully.',
            'service' => $service
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized. Hanya admin yang dapat mengupdate data Dinas.'
            ], 403);
        }

        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'message' => 'Data dinas tidak ditemukan.'
            ], 404);
        }

        $validated = $request->validate([
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('profile')) {
            // Hapus foto profile lama kalau ada
            if ($service->profile && Storage::disk('public')->exists($service->profile)) {
                Storage::disk('public')->delete($service->profile);
            }

            // Upload foto baru
            $profilePath = $request->file('profile')->store(
                'services/profiles',
                'public'
            );

            $service->profile = $profilePath;
        }

        // Update field lainnya
        $service->description = $validated['description'] ?? $service->description;
        $service->location = $validated['location'] ?? $service->location;
        $service->latitude = $validated['latitude'] ?? $service->latitude;
        $service->longitude = $validated['longitude'] ?? $service->longitude;

        $service->save();

        return response()->json([
            'message' => 'Service updated successfully.',
            'service' => $service
        ]);
    }
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized. Only admin can delete service.'
            ], 403);
        }

        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'message' => 'Data dinas tidak ditemukan.'
            ], 404);
        }

        // Hapus file foto profile kalau ada
        if ($service->profile && Storage::disk('public')->exists($service->profile)) {
            Storage::disk('public')->delete($service->profile);
        }

        // Hapus data service
        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully.'
        ]);
    }
}
