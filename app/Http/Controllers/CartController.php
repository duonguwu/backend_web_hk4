<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function getCartItems(Request $request)
    {
        // Kiểm tra xem token có tồn tại trong yêu cầu không
        $token = $request->header('Authorization');
        //return response()->json($token);
        $tokenModel = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->first();

        $user = User::find($tokenModel->tokenable_id);
        if ($user) {
            // Lấy giỏ hàng của người dùng
            // $cartItems = $user->cart->cartDetails;
            //return response()->json($user->cart->cartDetails);
            if ($user->cart) {
                // Lấy danh sách các sản phẩm trong giỏ hàng của người dùng
                $cartItems = $user->cart->cartDetails;
                $formattedCartItems = $cartItems->map(function ($cartItem) {
                    return [
                        '_id' => $cartItem->product_id,
                        'qty' => $cartItem->quantity,
                        'name' => $cartItem->product->name,
                        'image' => $cartItem->product->image,
                        'newPrice' => $cartItem->product->newPrice,
                        'price' => $cartItem->product->price,
                        'quantity' => $cartItem->product->quantity,
                        // Thêm các thông tin khác nếu cần

                    ];
                });
                return response()->json(['cart' => $formattedCartItems]);
            } else {
                return response()->json(['cart' => []]); // Giỏ hàng không tồn tại, trả về mảng rỗng hoặc thông tin phù hợp với trạng thái không có giỏ hàng.
            }
        }
        return response()->json([
            'status' => 401,
            'errors' => ['User not authenticated'],
        ], 401);
    }

    public function addToCart(Request $request)
    {
        $token = $request->header('Authorization');

        $tokenModel = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->first();
        $user = User::find($tokenModel->tokenable_id);
        try {
            // Lấy dữ liệu sản phẩm từ yêu cầu
            $productData = $request->input('product');
            //return response()->json($productData);
            // Kiểm tra sự tồn tại của sản phẩm
            $product = Product::where('_id', $productData['_id'])->first();
            //return response()->json($product);
            if (!$product) {
                return response()->json([
                    'status' => 404,
                    'errors' => ['Product not found'],
                ], 404);
            }

            // Kiểm tra sự tồn tại của giỏ hàng
            $cart = $user->cart;
            //return response()->json($cart);
            // Kiểm tra sự tồn tại của chi tiết giỏ hàng
            $cartDetail = CartDetail::where('cart_id', $cart->id)
                ->where('product_id', $product->_id)
                ->first();
            if ($cartDetail) {
                // Nếu chi tiết giỏ hàng đã tồn tại, tăng số lượng
                $cartDetail->quantity += 1;
                $cartDetail->save();
            } else {
                // Nếu chưa tồn tại, tạo mới chi tiết giỏ hàng
                $cartDetail = new CartDetail([
                    'cart_id' => $cart->id,
                    'product_id' => $product->_id,
                ]);
                $cartDetail->save();
            }
            //return response()->json($cartDetail);

            // Trả về phản hồi thành công
            return response()->json([
                'status' => 200,
                'message' => 'Product added to cart successfully',
                'cart' => $user->cart,
            ], 200);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateProductQty($productId, Request $request)
    {
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

        $cart = $user->cart;
        // Lấy chi tiết giỏ hàng của sản phẩm cần cập nhật
        $cartDetail = CartDetail::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();
        if (!$cartDetail) {
            return response()->json([
                'status' => 404,
                'errors' => ['Product not found in cart'],
            ], 404);
        }
        // Xử lý hành động cập nhật
        $actionType = data_get($request->json()->all(), 'action.type');

        if ($actionType === 'increment') {
            $cartDetail->quantity += 1;
        } elseif ($actionType === 'decrement' && $cartDetail->quantity > 1) {
            $cartDetail->quantity -= 1;
        } else {
            // Nếu số lượng là 1 và người dùng muốn giảm, hãy xóa sản phẩm khỏi giỏ hàng
            $cartDetail->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Product removed from cart successfully',
            ], 200);
        }

        // Lưu thay đổi
        $cartDetail->save();

        return response()->json([
            'status' => 200,
            'message' => 'Product quantity updated successfully',
            'cart' => $user->cart->cartDetails,
        ], 200);
    }

    public function removeFromCart($productId, Request $request)
    {
        // Xử lý xóa sản phẩm khỏi giỏ hàng
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

        $cart = $user->cart;
        // Lấy chi tiết giỏ hàng của sản phẩm cần cập nhật
        $cartDetail = CartDetail::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();
        if (!$cartDetail) {
            return response()->json([
                'status' => 404,
                'errors' => ['Product not found in cart'],
            ], 404);
        }
        $cartDetail->delete();

        $cartItems = $this->getCartItems($request)->getData();
        $cartData = isset($cartItems->cart) ? json_decode(json_encode($cartItems->cart), true) : [];

        return response()->json([
            'status' => 200,
            'message' => 'Product removed from cart successfully',
            'cart' => $cartData,
        ], 200);
    }
}
