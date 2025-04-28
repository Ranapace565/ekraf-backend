<?php

namespace App\Http\Controllers\Sector;

use App\Models\Sector;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SectorController extends Controller
{
    public function index()
    {
        $sectors = Sector::all();

        $formattedSectors = $sectors->map(function ($sector) {
            return [
                'id' => $sector->id,
                'name' => $sector->name,
                'description' => $sector->description,
                'icon_url' => $sector->icon ? asset('storage/' . $sector->icon) : null,
                'created_at' => $sector->created_at,
                'updated_at' => $sector->updated_at,
            ];
        });

        return response()->json([
            'sectors' => $formattedSectors
        ]);
    }

    public function show($id)
    {
        $sector = Sector::find($id);

        if (!$sector) {
            return response()->json([
                'message' => 'Sector not found.'
            ], 404);
        }

        return response()->json([
            'sector' => [
                'id' => $sector->id,
                'name' => $sector->name,
                'description' => $sector->description,
                'icon_url' => $sector->icon ? asset('storage/' . $sector->icon) : null,
                'created_at' => $sector->created_at,
                'updated_at' => $sector->updated_at,
            ]
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sectors,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg,gif|max:2048',
        ]);

        $iconPath = null;

        if ($request->hasFile('icon')) {
            $nameSlug = Str::slug($request->input('name'));
            $extension = $request->file('icon')->getClientOriginalExtension();
            $filename = $nameSlug . '.' . $extension;

            $iconPath = $request->file('icon')->storeAs(
                'sectors/icons',
                $filename,
                'public'
            );
        }

        $sector = Sector::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $iconPath,
        ]);

        return response()->json([
            'message' => 'Sector created successfully',
            'sector' => $sector
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $sector = Sector::find($id);

        if (!$sector) {
            return response()->json([
                'message' => 'Sector not found.'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sectors,name,' . $sector->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg,gif|max:2048',
        ]);

        // Cek jika ada upload file baru
        if ($request->hasFile('icon')) {
            // Hapus icon lama kalau ada
            if ($sector->icon && Storage::disk('public')->exists($sector->icon)) {
                Storage::disk('public')->delete($sector->icon);
            }

            $nameSlug = Str::slug($request->input('name'));
            $extension = $request->file('icon')->getClientOriginalExtension();
            $filename = $nameSlug . '.' . $extension;

            $iconPath = $request->file('icon')->storeAs(
                'sectors/icons',
                $filename,
                'public'
            );

            $sector->icon = $iconPath;
        }

        // Update field lainnya
        $sector->name = $validated['name'];
        $sector->description = $validated['description'] ?? null;
        $sector->save();

        return response()->json([
            'message' => 'Sector updated successfully',
            'sector' => $sector
        ]);
    }

    public function destroy($id)
    {
        $sector = Sector::find($id);

        if (!$sector) {
            return response()->json([
                'message' => 'Sector not found.'
            ], 404);
        }

        // Hapus icon dari storage kalau ada
        if ($sector->icon && Storage::disk('public')->exists($sector->icon)) {
            Storage::disk('public')->delete($sector->icon);
        }

        $sector->delete();

        return response()->json([
            'message' => 'Sector deleted successfully.'
        ]);
    }
}
