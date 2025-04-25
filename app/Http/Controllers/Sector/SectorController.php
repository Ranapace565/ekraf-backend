<?php

namespace App\Http\Controllers\Sector;

use App\Models\Sector;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SectorController extends Controller
{
    public function index(Request $request)
    {
        $query = Sector::query();

        // Filter berdasarkan nama sektor
        // if ($request->has('name')) {
        //     $query->where('name', $request->name);
        // }

        // Search berdasarkan nama usaha
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Paginasi
        $perPage = $request->input('per_page', 10);
        $businesses = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($businesses);
    }
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
    public function destroy($id)
    {
        $business = Sector::find($id);

        if (!$business) {
            return response()->json([
                'message' => 'Data sektor tidak ditemukan.'
            ], 404);
        }

        $business->delete();

        return response()->json([
            'message' => 'Sektor berhasil dihapus.'
        ]);
    }
}
