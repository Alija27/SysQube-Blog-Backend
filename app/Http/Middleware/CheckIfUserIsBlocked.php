<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfUserIsBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::where("email", $request->email)->first();
        if(!$user){
            return response()->json(["message"=>"The provided email doesn't exist"], 403);
        }
        if ($user && $user->is_blocked) {
            return response()->json(["message" => "Your account is blocked due to three failed attempts. Contact the administrator to unblock your account."], 403);
        }
        return $next($request);
    }
}
