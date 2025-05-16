<?php

namespace App\Http\Controllers\Event;

use App\Models\User;
use App\Models\Business;
use App\Mail\EventReject;
use App\Mail\EventApprove;
use App\Mail\EventDestroy;
use App\Mail\EventDisable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

        $business = Business::find($submission->business_id);

        $user = User::find(Auth::id());

        if (!$submission) {
            return response()->json([
                'message' => 'Data event tidak ditemukan.'
            ], 404);
        }

        if ($user->role != 'admin') {
            return response()->json([
                'message' => 'Anda tidak memiliki hak mensetujui event ini.'
            ], 403);
        }

        $entre = User::find($business->user_id);
        Mail::to($entre->email)->send(new EventApprove($entre, $submission->title));

        $submission->update([
            'is_approve' => 1,
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

        $business = Business::find($submission->business_id);

        $user = User::find(Auth::id());

        if (!$submission) {
            return response()->json([
                'message' => 'Data event tidak ditemukan.'
            ], 404);
        }

        if ($user->role != 'admin') {
            return response()->json([
                'message' => 'Anda tidak memiliki hak menolak event ini.'
            ], 403);
        }

        $request->validate([
            'note' => 'required|string|max:255'
        ]);

        $submission->update([
            'status' => 0,
            'note' => $request->note
        ]);

        $entre = User::find($business->user_id);
        Mail::to($entre->email)->send(new EventReject($entre, $submission->title, $request->note));

        return response()->json([
            'message' => 'Pengajuan ditolak.',
            'data' => $submission
        ]);
    }


    public function disable(Request $request, $id)
    {
        $submission = Event::findOrFail($id);

        $business = Business::find($submission->business_id);

        $user = User::find(Auth::id());

        if (!$submission) {
            return response()->json([
                'message' => 'Data event tidak ditemukan.'
            ], 404);
        }

        if ($user->role != 'admin') {
            return response()->json([
                'message' => 'Anda tidak memiliki hak menonaktifkan event ini.'
            ], 403);
        }

        $request->validate([
            'note' => 'required|string|max:255'
        ]);

        $submission->update([
            'status' => 0,
            'note' => $request->note
        ]);

        $entre = User::find($business->user_id);
        Mail::to($entre->email)->send(new EventDisable($entre, $submission->title, $request->note));

        return response()->json([
            'message' => 'Event Disable.',
            'data' => $submission
        ]);
    }


    public function destroy(Request $request, $id)
    {
        $event = Event::find($id);

        $business = Business::find($event->business_id);

        $user = User::find(id: Auth::id());

        if (!$event) {
            return response()->json([
                'message' => 'Data event tidak ditemukan.'
            ], 404);
        }

        if ($user->role != 'admin') {
            return response()->json([
                'message' => 'Anda tidak memiliki hak menghapus event ini.'
            ], 403);
        }

        $request->validate([
            'note' => 'required|string|max:255'
        ]);

        $entre = User::find($business->user_id);
        Mail::to($entre->email)->send(new EventDestroy($entre, $event->title, $request->note));

        $event->delete();

        return response()->json([
            'message' => 'Event berhasil dihapus.'
        ], 200);
    }
}
