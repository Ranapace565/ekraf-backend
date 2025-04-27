<?php

namespace App\Http\Controllers\Event;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class AdminEventController extends Controller
{
    public function index(Request $request)
    {
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
            'message' => 'Event ditemukan',
            'data' => $event
        ]);
    }

    public function show($id)
    {
        $event = Event::where('id', $id)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event tidak ditemukan '
            ], 404);
        }

        return response()->json([
            'message' => 'Event ditemukan',
            'data' => $event
        ], 200);
    }

    public function approve($id)
    {
        $submission = Event::findOrFail($id);

        // $request->validate([
        //     'note' => 'required|string|max:255'
        // ]);

        $submission->update([
            'status' => 1,
            // 'note' => $request->note
        ]);

        return response()->json([
            'message' => 'Pengajuan diterima.',
            'data' => $submission
        ]);
    }

    // tambah note
    public function reject(Request $request, $id)
    {
        $submission = Event::findOrFail($id);

        $request->validate([
            'note' => 'required|string|max:255'
        ]);

        $submission->update([
            'status' => 0,
            'note' => $request->note
        ]);

        return response()->json([
            'message' => 'Pengajuan ditolak.',
            'data' => $submission
        ]);
    }


    public function destroy($id)
    {

        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'message' => 'Data usaha tidak ditemukan.'
            ], 404);
        }
        $event->delete();

        return response()->json([
            'message' => 'Event berhasil dihapus.'
        ], 200);
    }
}
