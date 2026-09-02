<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ConvertsToWebp;

class AgentPageEditController{

  use ConvertsToWebp;

  public function getAgent(string $username){

    $result = DB::table('agent_page_edit')->where('username', $username)->get();
    return response()->json($result, 200);
    
  }

  public function editAgent(Request $request){

    if($request->hasfile('image_url')){
      $image_url = $this->convertToWebp($request->file('image_url'));
      DB::table('agent_page_edit')->update(['image_url' => $image_url]);
    }
    if($request->filled('name')){
      $name = $request->input('name');
      DB::table('agent_page_edit')->update(['name' => $name]);
    }
    if($request->filled('bio')){
      $bio = $request->input('bio');
      DB::table('agent_page_edit')->update(['bio' => $bio]);
    }
    if($request->filled('wa_link')){
      $wa_link = $request->input('wa_link');
      DB::table('agent_page_edit')->update(['wa_link' => $wa_link]);
    }

    return response()->json('Agent Page Berhasil Diperbarui!', 200);

  }

  public function deleteAgent(string $username){
    $result = DB::table('agent_page_edit')->where('username', $username)->delete();
    return response()->json($result, 200);
  }

}