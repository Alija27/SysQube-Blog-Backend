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
    public function register(Request $request){

        $user=$request->validate([
            "name"=>"required|string",
            "email"=>"required|email|unique:users,email",
            "password"=>"required|min:8|max:20",
        ]);

        $user=User::create($user);
        
        return response()->json(["message"=>"User registered successfully","user"=>new AuthResource($user)]);
    }

    public function login(Request $request){
        $request->validate([
            "email"=>"required|email",
            "password"=>"required|string|min:8|max:20",
        ]);

        $user=User::where("email",$request->email)->first();

        if(!$user || !Hash::check ($request->password,$user->password)){
            throw ValidationException::withMessages([
                "email"=>"The provided email is not correct",
                "password"=>"The provided password is not correct",
            ]);
        }

        return response()->json([
            "token"=>$user->createToken(Str::random(10))->plainTextToken,
            "user"=>new AuthResource($user)
        ]);
    }
}

