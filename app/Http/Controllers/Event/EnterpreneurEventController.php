<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Business\EntrepreneurBusinessController;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;

class EnterpreneurEventController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::id();

        $event = Event::where('user_id', $user)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Tidak memiliki event'
            ]);
        }
        // 
        $query = Event::query();

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
        $user = Auth::id();

        $event = Event::where('user_id', $user)->get();

        if (!$event) {
            return response()->json([
                'message' => 'Tidak memiliki event'
            ]);
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
        ]);

        $submission = Event::create([
            'user_id' => Auth::id(),
            'business_id' => $business->id,
            'title' => $request->title,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->description,
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

        if ($event->user_id != Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki hak untuk mengedit event ini.'
            ], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'location' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'description' => 'required|string', // perbaikan dari 'text' jadi 'string'
        ]);

        $event->update([
            'title' => $request->title,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->description,
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
                'message' => 'Data usaha tidak ditemukan.'
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
