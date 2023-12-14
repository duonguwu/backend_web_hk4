<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class CheckWishlist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
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
        if (!$user->wishlist) {
            $wishlist = new Wishlist();
            $wishlist->user_id = $user->id;
            $wishlist->save();
        }
        return $next($request);
    }
}
