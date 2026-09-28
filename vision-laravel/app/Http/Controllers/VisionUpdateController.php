<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ConvertsToWebp;

class VisionUpdateController{

  use ConvertsToWebp;

  public function getContent(){
    $result = DB::table('vision_update_section')->orderByDesc('id')->get();
    return response()->json($result, 200);
  }

  public function createContent(Request $request){

    if(!$request->hasFile('image_url')){
      return response()->json(['message' => 'Gambar Harus Diunggah!'], 400);
    }
    if(!$request->input('image_type')){
      return response()->json(['message' => 'Tipe Gambar harus Diisi!'], 400);
    }
    if(!$request->input('text_title')){
      return response()->json(['message' => 'Judul Update harus Diisi!'], 400);
    }
    if(!$request->input('text_body')){
      return response()->json(['message' => 'Deskripsi Update harus Diisi!'], 400);
    }

    $image_type = $request->input('image_type');
    $title = $request->input('text_title');
    $text_body = $request->input('text_body');
    $image = $this->convertToWebp($request->file('image_url'));

    $id = DB::table('vision_update_section')->insertGetId([
      'image_type' => $image_type,
      'image_url' => $image,
      'text_title' => $title,
      'text_body' => $text_body
    ]);

    return response()->json(['message' => 'Update Berhasil Dibuat!', 'id' => $id], 200);

  }

  public function updateImageContent(Request $request, int $id, string $imageType){

    if(!$request->hasFile('image_url')){
      return response()->json(['message' => 'Gambar Harus Diunggah!'], 400);
    }

    $exists = DB::table('vision_update_section')
      ->where('id', $id)
      ->where('image_type', $imageType)
      ->exists();

    if(!$exists){
      return response()->json(['message' => 'Update Tidak Ditemukan!'], 404);
    }

    $image = $this->convertToWebp($request->file('image_url'));

    DB::table('vision_update_section')
      ->where('id', $id)
      ->where('image_type', $imageType)
      ->update(['image_url' => $image]);

    return response()->json(['message' => 'Gambar Berhasil Diperbarui!'], 200);
  }

  public function updateTextContent(Request $request, int $id){

    if(!DB::table('vision_update_section')->where('id', $id)->exists()){
      return response()->json(['message' => 'Update Tidak Ditemukan!'], 404);
    }

    $data = [];

    if($request->filled('text_title')){
      $data['text_title'] = $request->input('text_title');
    }
    if($request->filled('text_body')){
      $data['text_body'] = $request->input('text_body');
    }

    if(empty($data)){
      return response()->json(['message' => 'Tidak Ada Perubahan Untuk Disimpan!'], 400);
    }

    DB::table('vision_update_section')->where('id', $id)->update($data);

    return response()->json(['message' => 'Konten Berhasil Diperbarui!'], 200);

  }

  public function deleteContent(int $id){

    $deleted = DB::table('vision_update_section')->where('id', $id)->delete();

    if(!$deleted){
      return response()->json(['message' => 'Update Tidak Ditemukan!'], 404);
    }

    return response()->json(['message' => 'Update Berhasil Dihapus!'], 200);
  }

  public function deleteImage(int $id, string $imageType){

    $deleted = DB::table('vision_update_section')
      ->where('id', $id)
      ->where('image_type', $imageType)
      ->delete();

    if(!$deleted){
      return response()->json(['message' => 'Gambar Tidak Ditemukan!'], 404);
    }

    return response()->json(['message' => 'Gambar Berhasil Dihapus!'], 200);
  }

}