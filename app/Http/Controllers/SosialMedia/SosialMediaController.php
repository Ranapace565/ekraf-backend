<?php

namespace App\Http\Controllers\SosialMedia;

use App\Models\SosialMedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SosialMediaController extends Controller
{
    public function index($businessId)
    {

        $sosialMedias = SosialMedia::where('business_id', $businessId)->get();

        if ($sosialMedias->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada sosial media untuk bisnis ini.'
            ], 404);
        }

        return response()->json([
            'message' => 'Daftar sosial media berdasarkan bisnis.',
            'data' => $sosialMedias,
        ], 200);
    }
}
