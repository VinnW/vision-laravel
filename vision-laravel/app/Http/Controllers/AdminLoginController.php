<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminLoginController{
  
  public function login(Request $request){
    $username = $request->input('username', $request->query('username'));
    $password = $request->input('password', $request->query('password'));

    if(!$username || !$password){
      return response()->json(['message' => "Username atau Password tidak boleh kosong!"], 400);
    }

    $result = DB::table('admin_login')->where('username', $username)->where('password', $password)->first();

    if($result){
      return response()->json(['message' => "Login Berhasil!"], 200);
    }
    else{
      return response()->json(['message' => "Username atau Password Salah!"], 400);
    }

  }
}