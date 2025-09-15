<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
  public function register(Request $request): JsonResponse
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role' => 'customer',
    ]);

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
      'message' => 'User registered successfully',
      'data' => $user,
      'token' => $token,
    ], 201);
  }

  public function login(Request $request): JsonResponse
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
      throw ValidationException::withMessages([
        'email' => ['The provided credentials do not match our records.'],
      ]);
    }

    $user = Auth::user();
    $user->tokens()->delete();
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
      'message' => 'Login successful',
      'data' => $user,
      'token' => $token,
    ]);
  }


  public function logout(Request $request): JsonResponse
  {
    $request->user()->tokens()->delete();

    return response()->json([
      'message' => 'Logout successful',
    ]);
  }
}
