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

    $username = session('agent_username');

    if (!$username) {
      return response()->json('Silakan login terlebih dahulu.', 401);
    }

    $toUpdate = [];

    if($request->hasfile('image_url')){
      $image_url = $this->convertToWebp($request->file('image_url'));
      $toUpdate['image'] = $image_url;
    }
    if($request->filled('name')){
      $name = $request->input('name');
      $toUpdate['name'] = $name;
    }
    if($request->filled('bio')){
      $bio = $request->input('bio');
      $toUpdate['description'] = $bio;
    }
    if($request->filled('wa_link')){
      $wa_link = $request->input('wa_link');
      $toUpdate['link_wa'] = $wa_link;
    }

    if (count($toUpdate) === 0) {
      return response()->json('Tidak ada data yang diperbarui!', 400);
    }

    DB::table('agent_page_edit')->where('username', $username)->update($toUpdate);

    return response()->json('Agent Page Berhasil Diperbarui!', 200);

  }

  public function deleteAgent(string $username){
    $result = DB::table('agent_page_edit')->where('username', $username)->delete();
    return response()->json($result, 200);
  }

}