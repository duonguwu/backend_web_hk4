<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Product;
use App\Models\Address;

class CheckoutController extends Controller
{
    // Validate và xử lý dữ liệu từ request

    public function placeOrder(Request $request)
    {
        $token = $request->header('Authorization');
        $tokenModel = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->first();

        $user = User::find($tokenModel->tokenable_id);

        if ($user) {
            $addressData = data_get($request->json()->all(), 'address');
            // return response()->json(['addressData' => $addressData]);
            //return response()->json(['addressData' => $addressData['id']]);
            $address = Address::where('id', $addressData['id'])->first();
            if (!$address) {
                $addressData['user_id'] = $user->id;
                //return response()->json(['no addressData' => $addressData]);

                $address = new Address($addressData);

                $address->save();
            }
            // return response()->json(['no address' => $address->id]);
            // Xử lý lưu đơn hàng vào database

            // $productData = data_get($request->json()->all(), 'productList');
            //$product = Product::where('_id', $productData['productId'])->first();


            //return response()->json(['orderData' => $orderData]);
            //return response()->json(['totalItems' => data_get($request->json()->all(), 'actualPriceOfCart')]);
            $invoice = new Invoice([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'total_items' => data_get($request->json()->all(), 'totalItems'),
                'actual_price' => data_get($request->json()->all(), 'actualPriceOfCart'),
                'total_price' => data_get($request->json()->all(), 'totalPriceOfCartProducts'),
                'payment_method' => data_get($request->json()->all(), 'paymentMethod'),
            ]);
            //return response()->json(['invoice' => $invoice]);
            $invoice->save();

            // Lấy dữ liệu sản phẩm từ yêu cầu
            $productData = data_get($request->json()->all(), 'productList');
            //return response()->json(['product' => $productData]);
            //return response()->json(['product' => $productData]);
            // Lưu chi tiết đơn hàng
            foreach ($productData as $product) {
                $invoiceDetail = new InvoiceDetail([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product['productId'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                ]);
                //return response()->json(['invoiceDetail' => $invoiceDetail]);
                $invoiceDetail->save();
            }
            $cart = $user->cart;
            $cdt = $cart->cartdetails;
            $cdt->each->delete();
            return response()->json(['message' => 'Order placed successfully']);
        }

        return response()->json([
            'status' => 401,
            'errors' => ['User not authenticated'],
        ], 401);
    }
}
