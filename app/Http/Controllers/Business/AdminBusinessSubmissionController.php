<?php

namespace App\Http\Controllers\Business;

use App\Models\User;
use App\Models\Sector;
use App\Models\Business;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\SubmissionApproved;
use App\Mail\SubmissionRejected;
use App\Models\BusinessSubmission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminBusinessSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = BusinessSubmission::query();

        if ($request->has('sector')) {
            $query->where('sector_id', $request->sector);
        }

        if ($request->has('search')) {
            $query->where('business_name', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->input('per_page', 10);

        $businesses = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($businesses);
    }

    public function approve($id)
    {
        $submission = BusinessSubmission::findOrFail($id);

        if (!$submission) {
            // Jika pengajuan tidak ditemukan
            return response()->json([
                'message' => 'Pengajuan usaha tidak ditemukan'
            ], 404);
        }
        $business = Business::create([
            'user_id' => $submission->user_id,
            'sector_id' => $submission->sector_id,
            'business_name' => $submission->business_name,
            // 'slug' => Str::slug($submission->business_name) . '-' . uniqid(), 
            'owner_name' => $submission->owner_name,
            // 'profile' => $submission->description,
            'description' => $submission->description,
            'location' => $submission->location,
            'latitude' => $submission->latitude,
            'longitude' => $submission->longitude,
        ]);

        if ($submission->proof) {
            $proofPath = storage_path('app/public/' . $submission->proof);

            if (file_exists($proofPath)) {
                unlink($proofPath); // Menghapus file foto bukti
            }
        }

        $user = User::find($submission->user_id);
        if ($user) {
            $user->role = 'entrepreneur';
            $user->save();

            // Kirim email notifikasi
            Mail::to($user->email)->send(new SubmissionApproved($user, $submission->business_name));
        }

        $submission->delete();

        return response()->json(['message' => 'Pengajuan disetujui dan dipindahkan ke data usaha.']);
    }

    // tambah note
    public function reject(Request $request, $id)
    {
        $submission = BusinessSubmission::find($id);

        if (!$submission) {
            // Jika pengajuan tidak ditemukan
            return response()->json([
                'message' => 'Pengajuan usaha tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'note' => 'required|string|max:255'
        ]);

        // dd('semlekom');

        $submission->update([
            'status' => 0,
            'note' => $validated['note'],
        ]);

        $user = User::find($submission->user_id);
        if ($user) {
            Mail::to($user->email)->send(new SubmissionRejected($user, $submission->note));
        }

        return response()->json([
            'message' => 'Pengajuan ditolak berhasil dikirim.',
            'data' => $submission
        ]);
    }

    public function destroy($id)
    {
        $submission = BusinessSubmission::find($id);

        if (!$submission) {
            return response()->json([
                'message' => 'Pengajuan usaha tidak ditemukan'
            ], 404);
        }

        // Periksa apakah pengguna adalah admin atau pemilik pengajuan
        if ($submission->user_id != Auth::id() && Auth::user()->role != 'admin') {
            return response()->json([
                'message' => 'Anda tidak memiliki hak untuk menghapus pengajuan usaha ini.'
            ], 403);
        }

        // Hapus file proof jika ada
        if ($submission->proof) {
            $proofPath = storage_path('app/public/' . $submission->proof);

            if (file_exists($proofPath)) {
                unlink($proofPath); // Menghapus file bukti
            }
        }

        // Hapus data pengajuan usaha
        $submission->delete();

        return response()->json([
            'message' => 'Penghapusan pengajuan usaha berhasil'
        ]);
    }
}
