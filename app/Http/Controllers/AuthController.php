<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return $this->error('Invalid credentials', 401);
        }

        $request->user()->tokens()->delete();

        $token = $request->user()->createToken('auth_token')->plainTextToken;

        return $this->success(data: ['token' => $token], message: 'User logged in');
    }

    public function user(Request $request)
    {
        return $this->success($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->success(message: 'User logged out');
    }
}