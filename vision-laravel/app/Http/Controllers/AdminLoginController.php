<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController
{
    public function login(Request $request)
    {
        $username = $request->input('username', $request->query('username'));
        $password = $request->input('password', $request->query('password'));

        if (!$username || !$password) {
            return response()->json(['message' => "Username atau Password tidak boleh kosong!"], 400);
        }

        if (Auth::guard('admin')->attempt(['username' => $username, 'password' => $password])) {
            $request->session()->regenerate();

            return response()->json(['message' => "Login Berhasil!"], 200);
        }

        return response()->json(['message' => "Username atau Password Salah!"], 400);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => "Logout Berhasil!"], 200);
    }
}