<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ConvertsToWebp;

class VisionUpdateController{

  use ConvertsToWebp;

  public function getContent(){
    $result = DB::table('vision_update_section')->get();
    return response()->json([$result], 200);
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

    $result = DB::table('vision_update_section')->insert([
      'image_type' => $image_type,
      'image_url' => $image,
      'text_title' => $title,
      'text_body' => $text_body
    ]);

    return response()->json(['messsage' => 'Update Berhasil Dibuat!', $result], 200);
    
  }

  public function updateImageContent(Request $request, int $id, string $imageType){

    if($request->hasFile('image_url')){
      $image = $this->convertToWebp($request->file('image_url'));
      DB::table('vision_update_section')->where('id', $id)->where('image_type', $imageType)->update([
        'image_url' => $image
      ]);
    }

    return response()->json(['message' => 'Gambar Berhasil Diperbarui!'], 200);
  }

  public function updateTextContent(Request $request, int $id){

    if($request->filled('text_title')){
      $title = $request->input('text_title');
      DB::table('vision_update_section')->where('id', $id)->update(['text_title' => $title]);
    }
    if($request->filled('text_body')){
      $text_body = $request->input('text_body');
      DB::table('vision_update_section')->where('id', $id)->update(['text_body' => $text_body]);
    }

    return response()->json(['message' => 'Konten Berhasil Diperbarui!'], 200);

  }

  public function deleteContent(int $id){
    $result = DB::table('vision_update_section')->where('id', $id)->delete();
    return response()->json($result, 200);
  }

  public function deleteImage(int $id, string $imageType){
    $result = DB::table('vision_update_section')->where('id', $id)->where('image_type', $imageType)->delete();
    return response()->json($result, 200);
  }

}