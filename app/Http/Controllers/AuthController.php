<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $user = $request->validate([
            "name" => "required|string",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:8|max:20",
        ]);

        if ($request->email && User::where('email', $request->email)->exists()->where('is_blocked', true)) {
            throw ValidationException::withMessages([
                "email" => "The provided email is blocked deu to three failed login attempts.",
            ]);
        }

        $user = User::create($user);

        return response()->json(["message" => "User registered successfully", "user" => new AuthResource($user)]);
    }

    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required|string|min:8|max:20",
        ]);

        $user = User::where("email", $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {

            if ($user) {
                $this->incrementLoginAttempts($user);

                if ($user->failed_attempts >= 3 || $user->is_blocked) {
                    $this->lockUserAccount($user);
                    throw ValidationException::withMessages([
                        'email' => ['Your account is locked due to three failed login attempts.'],
                    ]);
                }
            }
            throw ValidationException::withMessages([
                "email" => "The provided email is not correct",
                "password" => "The provided password is not correct",
            ]);
        }

        return response()->json([
            "token" => $user->createToken(Str::random(10))->plainTextToken,
            "user" => new AuthResource($user)
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(["message" => "Logged out successfully"]);
    }


    protected function incrementLoginAttempts($user)
    {
        $user->increment('failed_attempts');
    }
    protected function lockUserAccount($user)
    {
        if ($user) {
            $user->update(['is_blocked' => true]);
        }
    }
}
