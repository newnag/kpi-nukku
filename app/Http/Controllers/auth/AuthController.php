<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        $user = User::where('email', $credentials['email'] ?? null)->first();
        if (! $user || ! Hash::check($credentials['password'] ?? '', $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        Auth::login($user);
        $request->session()->regenerate();
        return redirect('/home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}

