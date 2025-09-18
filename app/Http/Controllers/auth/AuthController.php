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
        // For tests and simplicity, redirect to a minimal home endpoint
        $redirect = '/home';

        // If the frontend expects JSON (AJAX login), return the target
        if ($request->expectsJson()) {
            return response()->json(['redirect' => $redirect]);
        }

        // For normal form posts: users go directly, others respect intended
        return redirect($redirect);
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
