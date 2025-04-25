<?php

namespace App\Http\Controllers\Business;

use App\Models\User;
use App\Models\Sector;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\BusinessSubmission;
use App\Http\Controllers\Controller;

class AdminBusinessController extends Controller
{
    public function approve($id)
    {
        $submission = BusinessSubmission::findOrFail($id);

        $sector = Sector::where('name', $submission->sektor)->first();

        if (!$sector) {
            return response()->json([
                'message' => 'Sektor dengan nama "' . $submission->sektor . '" tidak ditemukan.'
            ], 404);
        }

        Business::create([
            'user_id' => $submission->user_id,
            'business_name' => $submission->nama_usaha,
            'proof_photo' => 'profile.jpg',
            'location' => $submission->alamat,
            'owner_name' => $submission->nama_owner,
            'sector_id' => $sector->id,
            'is_approved' => 1,
        ]);

        $user = User::find($submission->user_id);
        if ($user) {
            $user->role = 'entrepreneur';
            $user->save();
        }

        $submission->delete();

        return response()->json(['message' => 'Pengajuan disetujui dan dipindahkan ke data usaha.']);
    }

    public function reject($id)
    {
        $submission = BusinessSubmission::findOrFail($id);
        $submission->delete();

        return response()->json(['message' => 'Pengajuan ditolak dan dihapus.']);
    }

    public function disable($id)
    {
        $usaha = Business::find($id);

        if (!$usaha) {
            return response()->json([
                'message' => 'Data usaha tidak ditemukan.'
            ], 404);
        }

        $usaha->update(['is_approved' => false]);

        return response()->json([
            'message' => 'Data usaha berhasil dinonaktifkan',
            'data' => $usaha
        ]);
    }
    public function activate($id)
    {
        $usaha = Business::find($id);

        if (!$usaha) {
            return response()->json([
                'message' => 'Data usaha tidak ditemukan.'
            ], 404);
        }

        $usaha->update(['is_approved' => true]);

        return response()->json([
            'message' => 'Data usaha berhasil dinonaktifkan',
            'data' => $usaha
        ]);
    }

    public function destroy($id)
    {
        $business = Business::find($id);

        if (!$business) {
            return response()->json([
                'message' => 'Data usaha tidak ditemukan.'
            ], 404);
        }

        $business->delete();

        return response()->json([
            'message' => 'Usaha berhasil dihapus.'
        ]);
    }
}
