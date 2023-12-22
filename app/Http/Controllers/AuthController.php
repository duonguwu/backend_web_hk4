<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        Mail::to($user->email)->send(new WelcomeMail());
        // Generate a token for the new user
        $token = $user->createToken('authToken')->accessToken;

        // Return the token
        return response()->json(['access_token' => $token], 200);
    }

    // public function getAuthenticatedUser(Request $request)
    // {
    //     // Lấy thông tin người dùng đã xác thực
    //     $user = $request->user();

    //     // Kiểm tra xem người dùng có tồn tại hay không
    //     if (!$user) {
    //         return response()->json([
    //             'status' => 401,
    //             'errors' => ['User not authenticated'],
    //         ], 401);
    //     }

    //     // Trả về thông tin người dùng dưới dạng JSON
    //     return response()->json(['user' => $user]);
    // }

    // public function getUserFromToken($token)
    // {
    //     // Tìm user thông qua personal access token
    //     $user = User::all();

    //     if ($user) {
    //         // Kiểm tra xem personal access token của user có trùng với token được cung cấp hay không
    //         $matchingToken = $user->tokens->where('id', $token)->first();

    //         if ($matchingToken) {
    //             // Token hợp lệ, trả về thông tin user
    //             return $user;
    //         }
    //     }

    //     return null;
    // }
    // // Sử dụng hàm trong controller
    // public function getUserInfo(Request $request)
    // {
    //     // Lấy giá trị của token từ header 'Authorization'
    //     $token = $request->header('authorization');

    //     // Tìm user dựa trên token
    //     $user = $this->getUserFromToken($token);
    //     return response()->json(['user' => $user]);

    //     // if ($user) {
    //     //     return response()->json(['user' => $user]);
    //     // }

    //     // return response()->json([
    //     //     'status' => 401,
    //     //     'errors' => ['User not authenticated'],
    //     // ], 401);
    // }
    public function getUserInfo(Request $request)
    {
        // Lấy giá trị của token từ header 'Authorization'
        $token = $request->header('Authorization');
        //return response()->json($token);
        //return response()->json(hash('sha256', $token));

        $tokenModel = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->first();
        //return response()->json($tokenModel);
        $user = User::find($tokenModel->tokenable_id);
        if ($user) {
            return response()->json(['user' => $user]);
        }

        return response()->json([
            'status' => 401,
            'errors' => ['User not authenticated'],
        ], 401);
    }



    public function index()
    {
        // Lấy danh sách sản phẩm từ bảng products
        $user = User::all();
        // Trả về danh sách sản phẩm dưới dạng JSON
        return response()->json(['user' => $user], 200);
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
            $token = $user->createToken('authToken')->plainTextToken;
            $user->remember_token = explode('|', $token)[1];
            $user->save();
            //var_dump($token);
            return response()->json(['encodedToken' => explode('|', $token)[1], 'foundUser' => $user], 200);
        } else {
            return response()->json(['errors' => ['Invalid credentials']], 401);
        }
    }
}
