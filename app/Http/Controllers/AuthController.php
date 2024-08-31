<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 400);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return $this->error('Unauthorized', 401);
        }

        $token = app('App\Services\TokenService')->generateToken($request->user());

        return $this->success(data: [
            'token_type' => 'bearer',
            'token' => $token->plainTextToken,
        ], message: 'User logged in');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'title' => 'required',
            'birth_date' => 'required|date|before:today|date_format:Y-m-d',
            'relationship' => 'required|in:single,married,divorced,widowed',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 400);
        }

        $validated = $validator->validated();

        $person = Person::create([
            'name' => $validated['name'],
            'title' => $validated['title'],
            'birth_date' => $validated['birth_date'],
            'relationship' => $validated['relationship']
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'user',
            'person_id' => $person->id
        ]);

        $token = app('App\Services\TokenService')->generateToken($user);

        return $this->success(data: [
            'user' => $user->load('person')->toArray(),
            'token_type' => 'bearer',
            'token' => $token->plainTextToken,
        ], message: 'User registered');
    }

    public function user(Request $request)
    {
        $user = $request->user();

        return $this->success($user);
    }

    public function logout(Request $request)
    {
        app('App\Services\TokenService')->revokeTokens($request->user());

        return $this->success(message: 'User logged out');
    }
}
