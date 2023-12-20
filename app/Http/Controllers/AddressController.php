<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function storeAddress(Request $request)
    {
        // Xác thực người dùng và lấy thông tin người dùng đang đăng nhập
        $token = $request->header('Authorization');
        //return response()->json($token);
        $tokenModel = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->first();

        $user = User::find($tokenModel->tokenable_id);

        // Kiểm tra xem người dùng có đăng nhập hay không
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Nhận dữ liệu từ front end
        $data = $request->json()->all();
        // return response()->json(['data' => $data], 401);
        // Thêm thông tin user_id vào dữ liệu
        $data['user_id'] = $user->id;
        // return response()->json(['data' => $data], 200);
        // Tạo và lưu địa chỉ vào cơ sở dữ liệu
        $address = Address::create($data);
        //return response()->json(['address' => $address], 200);
        return response()->json(['message' => 'Address saved successfully.', 'address' => $address], 200);
    }
    public function getAddress(Request $request)
    {
        // Xác thực người dùng và lấy thông tin người dùng đang đăng nhập
        $token = $request->header('Authorization');
        //return response()->json($token);
        $tokenModel = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->first();

        $user = User::find($tokenModel->tokenable_id);

        // Kiểm tra xem người dùng có đăng nhập hay không
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $address = $user->addresses;
        return response()->json(['addresses' => $address], 200);
        //return response()->json(['message' => 'Address saved successfully.', 'address' => $address], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
