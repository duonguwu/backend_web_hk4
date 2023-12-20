<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\WishlistDetails;

class WishlistController extends Controller
{
    public function getWishlistItems(Request $request)
    {
        // Kiểm tra xem token có tồn tại trong yêu cầu không
        $token = $request->header('Authorization');
        //return response()->json($token);
        $tokenModel = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->first();

        $user = User::find($tokenModel->tokenable_id);

        if ($user) {
            $wishlistItems = $user->wishlist->wishlistDetails;
            // return response()->json($wishlistItems);
            $formattedWishlistItems = $wishlistItems->map(function ($wishlistItems) {
                return [
                    '_id' => $wishlistItems->product_id,
                    'name' => $wishlistItems->product->name,
                    'image' => $wishlistItems->product->image,
                    'newPrice' => $wishlistItems->product->newPrice,
                    'price' => $wishlistItems->product->price,
                    // Thêm các thông tin khác nếu cần

                ];
            });
            return response()->json(['wishlist' => $formattedWishlistItems]);
        }
        return response()->json([
            'status' => 401,
            'errors' => ['User not authenticated'],
        ], 401);
    }

    public function addToWishlist(Request $request)
    {
        $token = $request->header('Authorization');

        $tokenModel = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->first();
        $user = User::find($tokenModel->tokenable_id);

        try {
            // Lấy dữ liệu sản phẩm từ yêu cầu
            $productData = data_get($request->json()->all(), 'product');
            //return response()->json($productData);
            //return response()->json($productData);
            // Kiểm tra sự tồn tại của sản phẩm
            $product = Product::where('_id', $productData['_id'])->first();
            //return response()->json($productData['_id']);
            if (!$product) {
                return response()->json([
                    'status' => 404,
                    'errors' => ['Product not found'],
                ], 404);
            }

            // Kiểm tra sự tồn tại của yêu thích
            $wishlist = $user->wishlist;
            // return response()->json($wishlist);
            // Kiểm tra sự tồn tại của chi tiết giỏ hàng
            $wishlistDetail = WishlistDetails::where('wishlist_id', $wishlist->id)
                ->where('product_id', $product->_id)
                ->first();
            // Nếu chưa tồn tại, tạo mới chi tiết giỏ hàng

            if ($wishlistDetail) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Product already in Wishlist.',
                    'wishlist' => $user->wishlist,
                ], 200);
            } else {
                $wishlistDetail = new WishlistDetails([
                    'wishlist_id' => $wishlist->id,
                    'product_id' => $product->_id,
                ]);
                $wishlistDetail->save();
            }

            // return response()->json($wishlistDetail);

            // Trả về phản hồi thành công
            return response()->json([
                'status' => 200,
                'message' => 'Product added to cart successfully',
                'wishlist' => $user->wishlist,
            ], 200);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function removeFromWishlist($productId, Request $request)
    {
        // Xử lý xóa sản phẩm khỏi yêu thích
        // Kiểm tra xem người dùng có đăng nhập không
        $token = $request->header('Authorization');
        $tokenModel = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->first();

        $user = User::find($tokenModel->tokenable_id);
        if (!$user) {
            return response()->json([
                'status' => 401,
                'errors' => ['User not authenticated'],
            ], 401);
        }
        // Kiểm tra sự tồn tại của yêu thích
        $wishlist = $user->wishlist;
        // return response()->json($wishlist);
        // Kiểm tra sự tồn tại của chi tiết giỏ hàng
        $wishlistDetail = WishlistDetails::where('wishlist_id', $wishlist->id)
            ->where('product_id', $productId)
            ->first();

        // return response()->json($wishlistDetail);

        if (!$wishlistDetail) {
            return response()->json([
                'status' => 404,
                'errors' => ['Product not found in wishlist'],
            ], 404);
        }
        $wishlistDetail->delete();
        $wishlistItems = $this->getWishlistItems($request)->getData();
        $wishlistData = isset($wishlistItems->wishlist) ? json_decode(json_encode($wishlistItems->wishlist), true) : [];

        return response()->json([
            'status' => 200,
            'message' => 'Product removed from wishlist successfully',
            'wishlist' => $wishlistData,
        ], 200);
    }
}
