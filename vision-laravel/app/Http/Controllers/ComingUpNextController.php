<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ConvertsToWebp;

class ComingUpNextController{

  use ConvertsToWebp;

  public function getContent(){
    $event = DB::table('coming_up_next_section')->get();
    return response()->json($event, 200);
  }

  public function createContent(Request $request){

    if(!$request->hasFile('image_url')){
     return response()->json(['message' => 'Gambar harus Diunggah!',], 400);
    }
    if(!$request->filled('description')){
      return response()->json(['message' => 'Deskripsi harus Diisi!'], 400);
    }

    $image = $this->convertToWebp($request->file('image_url'));
    $desc = $request->input('description');

    $result = DB::table('coming_up_next_section')->insert(
      [
        'image_url' => $image,
        'description' => $desc
      ]);

    return response()->json(['message' => 'Konten Berhasil Dibuat!', $result], 200);

  }

  public function updateContent(Request $request){
    $image = $this->convertToWebp($request->file('image_url'));
    $desc = $request->input('description');

    if(!$image && !$desc){
      return response()->json(['message' => "Gambar atau Deskripsi harus Diubah!"], 400);
    }

    if($image){
      DB::table('coming_up_next_section')->update(['image_url' => $image]);
    }
    if($desc){
      DB::table('coming_up_next_section')->update(['description' => $desc]);
    }
    return response()->json(['message' => "Konten Berhasil Diubah!"], 200);

  }
}