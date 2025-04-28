<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index($business_id)
    {
        $products = Product::where('business_id', $business_id)->get();

        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada produk yang ditemukan untuk bisnis ini.'
            ], 404);
        }

        return response()->json([
            'message' => 'Daftar produk.',
            'data' => $products,
        ], 200);
    }
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail produk.',
            'data' => $product,
        ], 200);
    }
}
