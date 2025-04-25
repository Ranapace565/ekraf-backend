<?php

namespace App\Http\Controllers\Business;

use App\Models\User;
use App\Models\Sector;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\BusinessSubmission;
use App\Http\Controllers\Controller;

class AdminBusinessSubmissionController extends Controller
{
    public function approve($id)
    {
        $submission = BusinessSubmission::findOrFail($id);

        $sector = Sector::where('name', $submission->sektor)->first();

        // if (!$sector) {
        //     return response()->json([
        //         'message' => 'Sektor dengan nama "' . $submission->sektor . '" tidak ditemukan.'
        //     ], 404);
        // }

        Business::create([
            'user_id' => $submission->user_id,
            'business_name' => $submission->business_name,
            'proof_photo' => 'profile.jpg',
            'location' => $submission->location,
            'owner_name' => $submission->owner_name,
            'sector_id' => $submission->sector_id,
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
        $submission->update(['status' => 0]);

        return response()->json([
            'message' => 'Pengajuan ditolak.',
            'data' => $submission
        ]);
    }

    public function destroy($id)
    {
        $submission = BusinessSubmission::findOrFail($id);
        $submission->delete();

        return response()->json(['message' => 'Pengajuan ditolak dan dihapus.']);
    }
}
