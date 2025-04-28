<?php

namespace App\Http\Controllers\Business;

use App\Models\User;
use App\Models\Business;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EntrepreneurBusinessController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $business = Business::where('user_id', $user->id)
            ->first();

        if (!$business) {
            return response()->json([
                'message' => 'Usaha Anda tidak ditemukan atau belum aktif.'
            ], 404);
        }

        if ($business->status != 1) {
            return response()->json([
                'id' => $business->id,
                'business_name' => $business->business_name,
                'slug' => $business->slug,
                'owner_name' => $business->owner_name,
                'description' => $business->description,
                'profile' => asset('storage/' . $business->profile), // URL profile (foto usaha)
                'location' => $business->location,
                'latitude' => $business->latitude,
                'longitude' => $business->longitude,
                'instagram' => $business->instagram,
                'facebook' => $business->facebook,
                'tiktok' => $business->tiktok,
                'status' => $business->status,
                'note' => $business->note,
                'created_at' => $business->created_at,
                'updated_at' => $business->updated_at,
            ]);
        }

        return response()->json([
            'id' => $business->id,
            'business_name' => $business->business_name,
            'slug' => $business->slug,
            'owner_name' => $business->owner_name,
            'description' => $business->description,
            'profile' => asset('storage/' . $business->profile),
            'location' => $business->location,
            'latitude' => $business->latitude,
            'longitude' => $business->longitude,
            'instagram' => $business->instagram,
            'facebook' => $business->facebook,
            'tiktok' => $business->tiktok,
            'status' => $business->status,
            'created_at' => $business->created_at,
            'updated_at' => $business->updated_at,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $business = Business::where('user_id', $user->id)
            ->first();

        if (!$business) {
            return response()->json([
                'message' => 'Usaha tidak ditemukan.'
            ], 404);
        }

        if ($business->user_id != Auth::id() && Auth::user()->role != 'admin') {
            return response()->json([
                'message' => 'Anda tidak memiliki hak untuk mengubah data usaha ini.'
            ], 403);
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'sector_id' => 'nullable|exists:sectors,id',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'status' => 'nullable|in:0,1,2', 
        ]);

        $profilePath = $business->profile;

        if ($request->hasFile('profile')) {
            if ($profilePath && Storage::exists('public/' . $profilePath)) {
                Storage::delete('public/' . $profilePath);
            }

            $slug = Str::slug($request->input('business_name'));
            $extension = $request->file('profile')->getClientOriginalExtension();
            $filename = $slug . '-' . time() . '.' . $extension;

            $profilePath = $request->file('profile')->storeAs(
                'businesses/profile',
                $filename,
                'public'
            );
        }

        if ($business->status == 0) {
            $newStatus = 2;
        } else {
            $newStatus = $validated['status'] ?? $business->status;
        }

        $business->update([
            'business_name' => $validated['business_name'],
            // 'slug' => Str::slug($validated['business_name']),
            'owner_name' => $validated['owner_name'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'instagram' => $validated['instagram'] ?? null,
            'facebook' => $validated['facebook'] ?? null,
            'tiktok' => $validated['tiktok'] ?? null,
            'sector_id' => $validated['sector_id'] ?? null,
            'profile' => $profilePath,
            'note' => null,
            'status' => $newStatus,
        ]);

        return response()->json([
            'message' => 'Update usaha berhasil',
            'business' => $business
        ]);
    }

    public function disable()
    {
        $user = Auth::user();

        $business = Business::where('user_id', $user->id)
            ->first();

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

        $business->active = false;
        $business->save();

        return response()->json([
            'message' => 'Usaha berhasil dinonaktifkan.',
            'business' => $business
        ]);
    }


    public function activate()
    {
        $user = Auth::user();

        $business = Business::where('user_id', $user->id)
            ->first();

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

        $business->active = false;
        $business->save();

        return response()->json([
            'message' => 'Usaha berhasil diaktifkan.',
            'business' => $business
        ]);
    }

    public function destroy()
    {
        $user = Auth::user();

        $business = Business::where('user_id', $user->id)
            ->first();

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
    public function FindByUser($id)
    {
        return Business::where('user_id', $id);
    }
}
