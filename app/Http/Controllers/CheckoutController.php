<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Product;
use App\Models\Address;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMail;

class CheckoutController extends Controller
{

    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }

    public function placeOrder(Request $request)
    {
        $token = $request->header('Authorization');
        $tokenModel = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->first();

        $user = User::find($tokenModel->tokenable_id);
        $paymentMethod = data_get($request->json()->all(), 'paymentMethod');

        if ($user) {
            $addressData = data_get($request->json()->all(), 'address');
            $address = Address::where('id', $addressData['id'])->first();
            if (!$address) {
                $addressData['user_id'] = $user->id;
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
            $invoice->save();
            Mail::to($user->email)->send(new OrderMail($invoice));

            // return response()->json($paymentMethod);
            if ($paymentMethod == 'vnpay') {
                $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
                $vnp_Returnurl = "http://localhost:3000/payment-result";
                $vnp_TmnCode = "ZBOE6FWZ"; //Mã website tại VNPAY 
                $vnp_HashSecret = "PHOVJRLHATMNUCXTKOVTATHXSQCVCYXD"; //Chuỗi bí mật

                $vnp_TxnRef = rand(00, 9999); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
                $vnp_OrderInfo = 'Noi dung thanh toan';
                $vnp_OrderType = 'billpayment';
                $vnp_Amount = data_get($request->json()->all(), 'totalPriceOfCartProducts') * 100;
                $vnp_Locale = 'vn';
                $vnp_BankCode = 'NCB';
                $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

                $inputData = array(
                    "vnp_Version" => "2.1.0",
                    "vnp_TmnCode" => $vnp_TmnCode,
                    "vnp_Amount" => $vnp_Amount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => date('YmdHis'),
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnp_IpAddr,
                    "vnp_Locale" => $vnp_Locale,
                    "vnp_OrderInfo" => $vnp_OrderInfo,
                    "vnp_OrderType" => $vnp_OrderType,
                    "vnp_ReturnUrl" => $vnp_Returnurl,
                    "vnp_TxnRef" => $vnp_TxnRef
                );

                if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                    $inputData['vnp_BankCode'] = $vnp_BankCode;
                }
                ksort($inputData);
                $query = "";
                $i = 0;
                $hashdata = "";
                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                    } else {
                        $hashdata .= urlencode($key) . "=" . urlencode($value);
                        $i = 1;
                    }
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }

                $vnp_Url = $vnp_Url . "?" . $query;
                if (isset($vnp_HashSecret)) {
                    $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
                    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
                }
                $returnData = array(
                    'code' => '00', 'message' => 'success', 'data' => $vnp_Url
                );
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
                    $productDB = Product::where('_id', $product['productId'])->first();
                    //return response()->json($productDB);
                    $productDB->quantity = $productDB->quantity - $product['quantity'];
                    $productDB->save(['_id' => $productDB->_id]);
                    $invoiceDetail->save();
                }
                return response()->json(['message' => 'Order placed successfully', 'vnpay_url' => $vnp_Url]);
            } else if ($paymentMethod == 'payUrl') {
                $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";


                $partnerCode = 'MOMOBKUN20180529';
                $accessKey = 'klm05TvNBzhg7h7j';
                $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
                $orderInfo = "Thanh toán qua MoMo";
                $amount = data_get($request->json()->all(), 'totalPriceOfCartProducts');
                $orderId = rand(00, 9999);
                $redirectUrl = "http://localhost:3000/payment-result";
                $ipnUrl = "http://localhost:3000/payment-result";
                $extraData = "";



                $partnerCode = $partnerCode;
                $accessKey = $accessKey;
                $serectkey = $secretKey;
                $orderId = rand(00, 9999); // Mã đơn hàng
                $orderInfo = $orderInfo;
                $amount = $amount;
                $ipnUrl = $ipnUrl;
                $redirectUrl = $redirectUrl;
                $extraData = $extraData;

                $requestId = time() . "";
                $requestType = "payWithATM";
                // $extraData = ($_POST["extraData"] ? $_POST["extraData"] : "");
                //before sign HMAC SHA256 signature
                $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
                $signature = hash_hmac("sha256", $rawHash, $serectkey);
                $data = array(
                    'partnerCode' => $partnerCode,
                    'partnerName' => "Test",
                    "storeId" => "MomoTestStore",
                    'requestId' => $requestId,
                    'amount' => $amount,
                    'orderId' => $orderId,
                    'orderInfo' => $orderInfo,
                    'redirectUrl' => $redirectUrl,
                    'ipnUrl' => $ipnUrl,
                    'lang' => 'vi',
                    'extraData' => $extraData,
                    'requestType' => $requestType,
                    'signature' => $signature
                );
                $result = $this->execPostRequest($endpoint, json_encode($data));
                $jsonResult = json_decode($result);  // decode json
                //return response()->json($jsonResult);
                //Just a example, please check more in there
                $momoUrl = $jsonResult->payUrl;

                //return response()->json($payUrl);
                //header('Location: ' . $jsonResult['payUrl']);
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
                    $productDB = Product::where('_id', $product['productId'])->first();
                    //return response()->json($productDB);
                    $productDB->quantity = $productDB->quantity - $product['quantity'];
                    $productDB->save(['_id' => $productDB->_id]);
                    $invoiceDetail->save();
                }
                return response()->json(['message' => 'Order placed successfully',  'payUrl' => $momoUrl]);
            } else {
                $productData = data_get($request->json()->all(), 'productList');
                foreach ($productData as $product) {
                    $invoiceDetail = new InvoiceDetail([
                        'invoice_id' => $invoice->id,
                        'product_id' => $product['productId'],
                        'quantity' => $product['quantity'],
                        'price' => $product['price'],
                    ]);
                    $productDB = Product::where('_id', $product['productId'])->first();
                    //return response()->json($productDB);
                    $productDB->quantity = $productDB->quantity - $product['quantity'];
                    $productDB->save(['_id' => $productDB->_id]);
                    $invoiceDetail->save();
                    return response()->json(['message' => 'Order placed successfully']);
                }
            }


            return response()->json(['message' => 'Order placed successfully']);
        }

        return response()->json([
            'status' => 401,
            'errors' => ['User not authenticated'],
        ], 401);
    }
    // test momo:
    // NGUYEN VAN A
    // 9704 0000 0000 0018
    // 03/07
    // OTP

    // Ngân hàng	NCB
    // Số thẻ	9704198526191432198
    // Tên chủ thẻ	NGUYEN VAN A
    // Ngày phát hành	07/15
    // Mật khẩu OTP	123456


    // public function handleVnpayReturn(Request $request)
    // {
    //     $vnp_TxnRef = $request->input('vnp_TxnRef'); // Mã đơn hàng
    //     $vnp_ResponseCode = $request->input('vnp_ResponseCode'); // Mã phản hồi
    //     $vnp_SecureHash = $request->input('vnp_SecureHash'); // Chuỗi bảo mật

    //     // Kiểm tra mã phản hồi
    //     if ($vnp_ResponseCode == '00') {
    //         // Giao dịch thành công, cập nhật trạng thái đơn hàng
    //         $invoice = Invoice::find($vnp_TxnRef);
    //         if ($invoice) {
    //             $invoice->status = 'paid';
    //             $invoice->save();
    //         }

    //         return response()->json(['message' => 'Payment successful']);
    //     } else {
    //         // Giao dịch không thành công
    //         return response()->json(['message' => 'Payment failed']);
    //     }
    // }

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

    public function getAllInvoice()
    {
        $invoice = Invoice::all();
        if ($invoice) {
            return response()->json($invoice);
        } else {
            return response()->json(['error' => 'Invoice not found'], 404);
        }
    }
    public function getAllInvoiceDetails($invoiceId)
    {
        $invoice = Invoice::with('details.product') // Sử dụng eager loading để lấy cả chi tiết sản phẩm
            ->find($invoiceId);
        if ($invoice) {
            return response()->json($invoice);
        } else {
            return response()->json(['error' => 'Invoice not found'], 404);
        }
    }
}
