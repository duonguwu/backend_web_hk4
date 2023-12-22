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
        $validatedData = $request->validate([
            'id' => 'required',
            'name' => 'required',
            'brand' => 'required',
            'category' => 'required',
            'gender' => 'required',
            'weight' => 'required',
            'quantity' => 'required',
            'image' => 'required',
            'rating' => 'required',
            'price' => 'required',
            'newPrice' => 'required',
            'trending' => 'required',
            'description' => 'required',
        ]);
        //return response()->json(['validatedData' => $validatedData],);
        // Save the image to public/assets
        //$imagePath = $request->file('image')->storeAs('assets', 'public');
        // file_put_contents(public_path('assets/test.txt'), 'Hello, World!');
        // return response()->json(['message' => 'Product added successfully'], 201);
        // Create a new product
        $product = new Product();
        $product->_id = $validatedData['id'];
        $product->name = $validatedData['name'];
        $product->brand = $validatedData['brand'];
        $product->category = $validatedData['category'];
        $product->gender = $validatedData['gender'];
        $product->weight = $validatedData['weight'];
        $product->quantity = $validatedData['quantity'];
        $product->image = $validatedData['image'];
        $product->rating = $validatedData['rating'];
        $product->price = $validatedData['price'];
        $product->newPrice = $validatedData['newPrice'];
        $product->trending = $validatedData['trending'];
        $product->description = $validatedData['description'];
        //return response()->json(['product' => $product],);
        // Save the product to the database
        $product->save();

        return response()->json(['message' => 'Product added successfully'], 201);
    }
}
