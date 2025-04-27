<?php

namespace App\Http\Controllers\Event;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $event = Event::where('is_approved', true);

        // Baru kalau mau search:
        if ($request->has('search')) {
            $event->where('title', 'like', '%' . $request->search . '%');
        }

        // Paginasi
        $perPage = $request->input('per_page', 10);
        $event = $event->orderBy('created_at', 'desc')->paginate($perPage);

        if ($event->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada event'
            ]);
        }

        return response()->json([
            'message' => 'Event milik pengusaha',
            'data' => $event,
        ], 200);
    }
    public function show($id)
    {

        // $event = Event::where('is_approved', true)->first();
        $event = Event::where('id', $id)->where('is_approved', true)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event tidak ditemukan'
            ]);
        }

        return response()->json([
            'message' => 'Event ditemukan',
            'data' => $event,
        ], 201);
    }
}
