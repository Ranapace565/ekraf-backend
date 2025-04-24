<?php

namespace App\Http\Controllers\Sector;

use App\Models\Sector;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SectorController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|unique:sectors,name',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $sector = Sector::create([
            'name'        => $request->name,
            'description' => $request->description,
            'icon'        => $request->icon,
        ]);

        return response()->json([
            'message' => 'Sektor berhasil ditambahkan.',
            'data'    => $sector
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $sector = Sector::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|unique:sectors,name,' . $sector->id,
            'description' => 'nullable|string',
            'icon'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $sector->update([
            'name'        => $request->name,
            'description' => $request->description,
            'icon'        => $request->icon,
        ]);

        return response()->json([
            'message' => 'Sektor berhasil diperbarui.',
            'data'    => $sector
        ]);
    }
}
