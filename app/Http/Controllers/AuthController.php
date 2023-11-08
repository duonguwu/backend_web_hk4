<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;


class AuthController extends Controller
{
    //Sign up
    public function register(Request $request)
    {
        // Validate input data
        $request->validate([
            'name' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Create a new user
        $user = User::create([
            'name' => $request->name, // Sửa từ $request->username thành $request->name
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Generate a token for the new user
        $token = $user->createToken('authToken')->accessToken;

        // Return the token
        return response()->json(['access_token' => $token], 200);
    }


    //Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            /** @var \App\Models\User $user **/ // đánh dấu đây là user từ Models
            $user = Auth::user();
            $token = $user->createToken('authToken')->accessToken;
            //var_dump($token);
            return response()->json(['encodedToken' => $token, 'foundUser' => $user], 200);
        } else {
            return response()->json(['errors' => ['Invalid credentials']], 401);
        }
        // if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        //     $user = Auth::user();
        //     /** @var \App\Models\User $user **/
        //     $token = $user->createToken('authToken');
        //     //var_dump($token->accessToken);

        //     if ($token) {
        //         $accessToken = $token->accessToken; // Lấy token từ đây
        //         return response()->json(['accessToken' => $accessToken, 'foundUser' => $user], 200);
        //     } else {
        //         return response()->json(['errors' => ['Token creation failed']], 500);
        //     }
        // } else {
        //     return response()->json(['errors' => ['Invalid credentials']], 401);
        // }

        // if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        //     // Đăng nhập thành công
        //     // Truy cập mã token bằng Passport::user()
        //     /** @var \App\Models\User $user **/
        //     $user = Auth::user();
        //     $token = $user->accessToken;

        //     // Trả về mã token trong phản hồi
        //     return response()->json(['access_token' => $token]);
        // } else {
        //     return response()->json(['errors' => ['Invalid credentials']], 401);
        // }
    }
}
