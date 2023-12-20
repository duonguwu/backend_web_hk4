<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Product; // Import model Product

class ProductController extends Controller
{


    public function index()
    {
        // Lấy danh sách sản phẩm từ bảng products
        $products = Product::all();
        // Trả về danh sách sản phẩm dưới dạng JSON
        return response()->json(['products' => $products], 200);
    }

    public function show($_id)
    {
        // Lấy thông tin sản phẩm theo ID
        $product = Product::where('_id', $_id)->first();

        // Kiểm tra xem sản phẩm có tồn tại hay không
        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        // Trả về thông tin sản phẩm dưới dạng JSON
        return response()->json(['products' => $product],);
    }
    public function addProduct(Request $request)
    {
        // Validate the request data
        $validatedData = $request->json()->all();

        // Save the image to public/assets
        $imagePath = $request->file('image')->store('assets', 'public');

        // Create a new product
        $product = new Product($validatedData);
        $product->image = $imagePath;

        // Save the product to the database
        $product->save();

        return response()->json(['message' => 'Product added successfully'], 201);
    }
}
