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
    public function getUserInvoices(Request $request)
    {
        $token = $request->header('Authorization');
        $tokenModel = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->first();

        $user = User::find($tokenModel->tokenable_id);
        $invoices = Invoice::where('user_id', $user->id)->get();

        return response()->json($invoices);
    }
    public function getInvoiceDetails($invoiceId, Request $request)
    {
        $token = $request->header('Authorization');
        $tokenModel = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->first();

        $user = User::find($tokenModel->tokenable_id);
        $invoice = Invoice::where('user_id', $user->id)
            ->with('details.product') // Sử dụng eager loading để lấy cả chi tiết sản phẩm
            ->find($invoiceId);

        if ($invoice) {
            return response()->json($invoice);
        } else {
            return response()->json(['error' => 'Invoice not found'], 404);
        }
    }
}
