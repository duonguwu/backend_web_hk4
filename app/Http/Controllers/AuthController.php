<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Laravel\Passport\HasApiTokens;


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
            return response()->json(['encodedToken' => $token, 'foundUser' => $user], 200);
        } else {
            return response()->json(['errors' => ['Invalid credentials']], 401);
        }
    }
}
