<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JoinAgentController{

    // getForm untuk melihat semua form yang disubmit di dalam admin panel
    public function getForm(){
        $result = DB::table('join_agent')->get();
        return response()->json($result, 200);
    }

    
    // submitForm untuk memastikan setiap data diisi oleh calon Agent
    public function submitForm(Request $request){

    if(!$request->filled('nama')){
        return response()->json('Nama harus diisi!', 400);
    }
    if(!$request->filled('email')){
        return response()->json('Email harus diisi!', 400);
    }
    if(!filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)){
        return response()->json('Email tidak valid!', 400);
    }
    if(!$request->filled('kota')){
        return response()->json('Kota harus diisi!', 400);
    }
    if(!$request->filled('no_telepon')){
        return response()->json('No Telepon harus diisi!', 400);
    }
    if(!$request->filled('pesan')){
        return response()->json('Pesan harus diisi!', 400);
    }

    $form = [
        "nama" => $request->input("nama"),
        "email" => $request->input("email"),
        "kota" => $request->input("kota"),
        "no_telepon" => $request->input("no_telepon"),
        "pesan" => $request->input('pesan')
    ];


    $hasil = DB::table('join_agent')->insert($form);
    return response()->json([
        'Form berhasil di submit!',
        $hasil,
        200
    ]);

    }

    public function deleteForm(String $username){
        $result = DB::table('join_agent')->where('username', $username)->delete();
        return response()->json([
            "Agent Form berhasil dihapus!",
            $result,
            200
        ]);
    }

}