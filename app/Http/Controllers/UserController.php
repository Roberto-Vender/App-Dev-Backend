<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $user = User::where('email', $email) -> first();
        if($user && Hash::check($password,$user->password) ){
            return response()->json(["message" => 'Login Successfully!','user'=>$user],200);
        }
        return response()->json(["message" => 'Incorrect username or password, Please try again!'],401);
    }

    /**
     * Show the form for creating a new resource.
     */

    public function register(Request $request)
    {
            $validated = $request->validate([
                "displayName" => 'required|string',
                'password' => 'required|string'
            ]);
            $validated['email'] = $request->input('email');

            $checkEmail = User::where('email', $validated['email'])->first();
            if($checkEmail){
            return response()->json(["message" => 'Email is already exist!'],422);
            }
            $validated['display_name'] = $validated['displayName'];
            unset($validated['displayName']);
            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);

            return response()->json([
                'message' => 'User created successfully!',
                'user' => $user
            ], 201);


    }


}
