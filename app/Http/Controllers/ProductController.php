<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use Illuminate\Http\Request;
use App\Models\Product; // Import model Product
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        // Lấy danh sách sản phẩm từ bảng products
        $products = Product::all();

        // Trả về danh sách sản phẩm dưới dạng JSON
        return response()->json(['products' => $products], 200);
    }

    public function show($id)
    {
        // Lấy thông tin sản phẩm theo ID
        $product = Product::find($id);

        // Kiểm tra xem sản phẩm có tồn tại hay không
        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        // Trả về thông tin sản phẩm dưới dạng JSON
        return response()->json(['products' => $product], 200);
    }

    public function store(ProductStoreRequest $request)
    {
        // $token = $request->header('Authorization');

        // $tokenModel = DB::table('personal_access_tokens')
        //     ->where('token', hash('sha256', $token))
        //     ->first();
        // $user = User::find($tokenModel->tokenable_id);

        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = uniqid('image_') . '.' . $image->getClientOriginalExtension();
                $image->storeAs('images', $filename, 'public');
                $uuid = Str::uuid()->toString();
                Product::create(
                    [
                        '_id' => $uuid,
                        'name' => $request->name,
                        'description' => $request->description,
                        'brand' => $request->brand,
                        'category' => $request->category,
                        'gender' => $request->gender,
                        'weight' => $request->weight,
                        'quantity' => $request->quantity,
                        'image' => $filename,
                        'rating' => $request->rating,
                        'price' => $request->price,
                        'newPrice' => $request->newPrice,
                        'trending' => $request->trending,
                    ]
                );
                return response()->json([
                    'message' => 'Product succesfully created'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Something went really wrong",
            ], 500);
        }
    }

    // public function update(ProductStoreRequest $request, $id)
    // {
    //     try {
    //         // Find product
    //         $product = Product::find($id);
    //         if (!$product) {
    //             return response()->json([
    //                 'message' => 'Product Not Found.'
    //             ], 404);
    //         }

    //         //echo "request : $request->image";
    //         $product->name = $request->name;
    //         $product->description = $request->description;

    //         if ($request->image) {

    //             // Public storage
    //             $storage = Storage::disk('public');

    //             // Old iamge delete
    //             if ($storage->exists($product->image))
    //                 $storage->delete($product->image);

    //             // Image name
    //             $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();
    //             $product->image = $imageName;

    //             // Image save in public folder
    //             $storage->put($imageName, file_get_contents($request->image));
    //         }

    //         // Update Product
    //         $product->save();

    //         // Return Json Response
    //         return response()->json([
    //             'message' => "Product successfully updated."
    //         ], 200);
    //     } catch (\Exception $e) {
    //         // Return Json Response
    //         return response()->json([
    //             'message' => "Something went really wrong!"
    //         ], 500);
    //     }
    // }
}
