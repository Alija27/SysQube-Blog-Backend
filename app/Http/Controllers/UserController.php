<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function destroy(User $user)
    {
         $user->delete();
        return response()->json(["message"=>"User deleted successfully"]);
    }
}