<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EntrepreneurProductController extends Controller
{
    //
    public function index()
    {
        // Ambil ID user yang login
        $userId = Auth::id();

        // Cari bisnis yang dimiliki user
        $business = Business::where('user_id', $userId)->first();

        if (!$business) {
            return response()->json([
                'message' => 'Bisnis tidak ditemukan untuk user ini.'
            ], 404);
        }

        // Ambil semua produk yang berelasi dengan bisnis tersebut
        $products = Product::where('business_id', $business->id)->get();

        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada produk untuk bisnis ini.'
            ], 404);
        }

        return response()->json([
            'message' => 'Daftar produk untuk bisnis Anda.',
            'data' => $products,
        ], 200);
    }

    public function show($id)
    {
        $userId = Auth::id();

        // Cari bisnis milik user
        $business = Business::where('user_id', $userId)->first();

        if (!$business) {
            return response()->json([
                'message' => 'Bisnis tidak ditemukan untuk user ini.'
            ], 404);
        }

        // Cari produk berdasarkan id dan pastikan produk milik bisnis user
        $product = Product::where('id', $id)
            ->where('business_id', $business->id)
            ->first();

        if (!$product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan atau bukan milik bisnis Anda.'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail produk.',
            'data' => $product,
        ], 200);
    }


    public function store(Request $request)
    {
        $business = Business::where('user_id', Auth::id())->first();

        if (!$business) {
            return response()->json(['message' => 'Anda harus memiliki usaha untuk dapat mengajukan event']);
        }
        // Validasi input dari request
        $validated = $request->validate([
            // 'business_id' => 'required|exists:businesses,id',  // Pastikan business_id valid
            'name' => 'required|string|max:255',  // Nama produk wajib diisi
            'price' => 'required|string|max:255', // Harga produk wajib diisi
            'detail' => 'nullable|string',  // Detail produk bersifat opsional
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Foto opsional, tapi harus gambar
        ]);

        // Jika ada file foto yang diupload, simpan di folder storage
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('products/photos', 'public');
        }

        // Buat produk baru dengan data yang sudah divalidasi
        $product = new Product();
        $product->business_id = $business->id;
        $product->name = $validated['name'];
        $product->price = $validated['price'];
        $product->detail = $validated['detail'] ?? null;  // Jika tidak ada detail, set null
        $product->photo = $photoPath;  // Jika ada foto, simpan path foto
        $product->save();

        // Kembalikan response sukses dengan data produk yang baru dibuat
        return response()->json([
            'message' => 'Produk berhasil ditambahkan.',
            'data' => $product,
        ], 201);
    }
    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }

        $userId = Auth::id();

        $business = Business::where('user_id', $userId)->first();

        if (!$business) {
            return response()->json([
                'message' => 'Bisnis tidak ditemukan untuk user ini.'
            ], 404);
        }

        if ($product->business_id !== $business->id) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk mengubah produk ini.'
            ], 403);
        }

        $photoPath = $product->photo;
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($photoPath) {
                $oldPhotoPath = storage_path('app/public/' . $photoPath);
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }

            $photoPath = $request->file('photo')->store('products/photos', 'public');
        }

        $product->name = $validated['name'];
        $product->price = $validated['price'];
        $product->detail = $validated['detail'] ?? null;
        $product->photo = $photoPath;
        $product->save();

        return response()->json([
            'message' => 'Produk berhasil diperbarui.',
            'data' => $product,
        ], 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }

        $userId = Auth::id();

        $business = Business::where('user_id', $userId)->first();

        if (!$business) {
            return response()->json([
                'message' => 'Bisnis tidak ditemukan untuk user ini.'
            ], 404);
        }

        if ($product->business_id !== $business->id) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk menghapus produk ini.'
            ], 403);
        }

        if ($product->photo) {
            $photoPath = storage_path('app/public/' . $product->photo);
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }

        $product->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus.',
        ], 200);
    }
}
