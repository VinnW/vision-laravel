<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreateAgentController{

    public function getAgents(){
        $result = DB::table('create_agent')->get();
        return response()->json($result, 200);
    }

    public function createAgent(Request $request){
        if(!$request->filled('username')){
            return response()->json("Username harus diisi!", 400);
        }
        if(!$request->filled('password')){
            return response()->json("Password harus diisi!", 400);
        }
        if(!$request->filled('link_page')){
            return response()->json("Link Page harus diisi!", 400);
        }

        $input = [
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'link_page' => $request->input('link_page')
        ];

        $result = DB::table('create_agent')->insert($input);
        return response()->json([
            "Agent berhasil dibuat!",
            $result,
            200
        ]);
    }

    public function resetPasswordAgent(String $username){
        $result = DB::table('create_agent')->where('username', $username)->update([
            'password' => ''
        ]);
        return response()->json([
            "Password Agent berhasil di reset!",
            $result,
            200
        ]);
    }

    public function deleteAgent(String $username){
        $result = DB::table('create_agent')->where('username', $username)->delete();
        return response()->json([
            "Agent berhasil dihapus!",
            $result,
            200
        ]);
    }

}