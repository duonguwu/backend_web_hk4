<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\CartDetail;

class CheckCart
{
    public function handle($request, Closure $next)
    {
        // // Kiểm tra xác thực người dùng
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

        // Kiểm tra giỏ hàng của người dùng
        if (!$user->cart) {
            $cart = new Cart();
            $cart->user_id = $user->id;
            $cart->save();
        }


        return $next($request);
    }
}
