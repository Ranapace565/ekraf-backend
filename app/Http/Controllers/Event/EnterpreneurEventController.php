<?php

namespace App\Http\Controllers\Event;

use App\Models\Event;
use App\Models\Business;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Business\EntrepreneurBusinessController;

class EnterpreneurEventController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::id();

        $query = Event::where('user_id', $user);

        // if (!$event) {
        //     return response()->json([
        //         'message' => 'Tidak memiliki event'
        //     ]);
        // }
        // 
        // $query = Event::query();

        if ($request->has('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        }

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Paginasi
        $perPage = $request->input('per_page', 10);
        $event = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'message' => 'Event milik pengusaha',
            'data' => $event,
        ], 201);
    }

    public function show($id)
    {
        $user = Auth::user();

        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event tidak ditemukan'
            ], 404);
        }

        if ($user->role != 'admin' && $event->user_id != $user->id) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk melihat event ini.'
            ], 403);
        }

        return response()->json([
            'message' => 'Event milik pengusaha',
            'data' => $event,
        ], 201);
    }

    public function store(Request $request)
    {
        $business = Business::where('user_id', Auth::id())->first();

        if (!$business) {
            return response()->json(['message' => 'Anda harus memiliki usaha untuk dapat mengajukan event']);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'location' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'description' => 'required|text',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            // Membuat slug untuk nama file berdasarkan judul event
            $slugTitle = Str::slug($request->input('title'));
            $extension = $request->file('thumbnail')->getClientOriginalExtension();
            $filename = $slugTitle . '.' . $extension;

            // Menyimpan file dengan nama sesuai dengan slug judul event
            $thumbnailPath = $request->file('thumbnail')->storeAs(
                'event/thumbnails',
                $filename,
                'public'
            );
        }

        $submission = Event::create([
            'user_id' => Auth::id(),
            'business_id' => $business->id,
            'title' => $request->title,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->description,
            'thumbnail' => $thumbnailPath,
        ]);

        return response()->json([
            'message' => 'Pengajuan event berhasil dikirim.',
            'data' => $submission,
        ], 201);
    }

    public function update($id, Request $request)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event tidak ditemukan.'
            ], 404);
        }

        if ($event->user_id != Auth::id() && !Auth::user()->is_admin) {
            return response()->json([
                'message' => 'Anda tidak memiliki hak untuk mengupdate event ini.'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'location' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $thumbnailPath = $event->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($thumbnailPath && Storage::exists('public/' . $thumbnailPath)) {
                Storage::delete('public/' . $thumbnailPath);
            }
            $slugTitle = Str::slug($request->input('title'));
            $extension = $request->file('thumbnail')->getClientOriginalExtension();
            $filename = $slugTitle . '.' . $extension;

            // Menyimpan file thumbnail baru
            $thumbnailPath = $request->file('thumbnail')->storeAs(
                'event/thumbnails',
                $filename,
                'public'
            );
        }

        $event->update([
            'title' => $validated['title'],
            // 'slug' => Str::slug($validated['title']) . '-' . uniqid(), // Update slug dengan judul baru
            'description' => $validated['description'],
            'event_date' => $validated['event_date'],
            'location' => $validated['location'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'note' => null,
            'is_approved' => 2,
            'thumbnail' => $thumbnailPath,
        ]);

        return response()->json([
            'message' => 'Event berhasil diperbarui.',
            'data' => $event,
        ], 200);
    }

    public function destroy($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'message' => 'Data event tidak ditemukan.'
            ], 404);
        }
        if ($event->user_id != Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki hak menghapus event ini.'
            ], 403);
        }

        $event->delete();

        return response()->json([
            'message' => 'Event berhasil dihapus.'
        ], 200);
    }
}
