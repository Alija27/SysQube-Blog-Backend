<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckIfUserIsBlocked;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post("/register" , [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"])->middleware(CheckIfUserIsBlocked::class);
Route::post("/logout",[AuthController::class,"logout"])->middleware('auth:sanctum');

Route::resource("posts", PostController::class)->except(["index","show"])->middleware('auth:sanctum');
Route::get("posts", [PostController::class, "index"]);
Route::get("posts/{post}", [PostController::class, "show"]);
Route::get("admin/posts", [PostController::class, "indexAdmin"])->middleware('auth:sanctum');

Route::resource("users", UserController::class)->except(["store","show","update"]);