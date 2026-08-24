<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ConvertsToWebp;

class HomeSectionController{
  
  use ConvertsToWebp;

  public function getBanners (){
    $result = DB::table('home_section')->get();
    return response()->json($result, 200);
  }

  public function createBanner (Request $request){
    if ($request->hasFile('banner_url')){
      $banner =  $this->convertToWebp($request->file('banner_url'));

      $id = DB::table('home_section')->insertGetId([
        'banner_url' => $banner
      ]);

      return response()->json([
        $id,
        'message' => "Gambar berhasil diunggah!",
        'image_url' => $banner
      ], 200);
    }
    else{
      return response()->json("Gambar Harus Diunggah!", 400);
    }
  }

  public function updateBanner (Request $request, int $id){
    if ($request->hasfile('banner_url')){
      $banner = $this->convertToWebp($request->file('banner_url'));

      $banner_update = DB::table('home_section')->where('id', $id)->update([$banner]);
      return response()->json([
        'message' => "Gambar Berhasil Diperbarui!",
        'updated_banner' => $banner_update
      ], 200);
    }
    else{
      return response()->json("Gambar Harus Diunggah!", 400);
    }
  }

  public function deleteBanner (int $id){
    $deleteBanner = DB::table('home_section')->where('id', $id)->delete();
    return response()->json(['message' => "Banner Berhasil Dihapus!"],200);
  }
}