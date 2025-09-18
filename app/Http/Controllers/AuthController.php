<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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

  public function forgotPassword(Request $request): JsonResponse
  {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
      $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
      ? response()->json(['message' => __($status)])
      : response()->json(['message' => __($status)], 400);
  }

  public function resetPassword(Request $request): JsonResponse
  {
    $request->validate([
      'token' => 'required',
      'email' => 'required|email',
      'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
      $request->only('email', 'password', 'password_confirmation', 'token'),
      function ($user, $password) {
        $user->forceFill([
          'password' => Hash::make($password)
        ])->setRememberToken(Str::random(60));

        $user->save();
      }
    );

    return $status === Password::PASSWORD_RESET
      ? response()->json(['message' => 'Your password has been changed. You may now login'])
      : response()->json(['message' => match ($status) {
        Password::INVALID_USER  => 'No account found with that email',
        Password::INVALID_TOKEN => 'Invalid request',
        default => 'Password reset failed. Please try again',
      }], 400);
  }
}
