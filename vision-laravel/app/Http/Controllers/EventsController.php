<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ConvertsToWebp;

class EventsController{

  use ConvertsToWebp;

  public function getEvent(){
    $result = DB::table('events_section')->get();
    return response()->json($result, 200);
  }

  public function createEvent(Request $request){

    if(!$request->hasFile('image_url')){
      return response()->json(['message' => 'Gambar Harus Diunggah!'], 400);
    }
    if(!$request->input('text_title')){
      return response()->json(['message' => 'Judul Harus Diisi!'], 400);
    }
    if(!$request->input('text_body')){
      return response()->json(['message' => 'Deskripsi Harus Diisi!'], 400);
    }

    $image = $this->convertToWebp($request->file('image_url'));
    $title = $request->input('text_title');
    $body = $request->input('text_body');

    $result = DB::table('events_section')->insert([
      'image_url' => $image,
      'text_title' => $title,
      'text_body' => $body
    ]);

    return response()->json($result, 200);
  }

  public function updateEvent(Request $request){
    
    if($request->hasfile('image_url')){
      $image = $this->convertToWebp($request->file('image_url'));
      DB::table('events_section')->update(['image_url' => $image]);
    }
    if($request->filled('text_title')){
      $title = $request->input('text_title');
      DB::table('events_section')->update(['text_title' => $title]);
    }
    if($request->filled('text_body')){
      $body = $request->input('text_title');
      DB::table('events_section')->update(['text_body' => $body]);
    }

    return response()->json(['message' => 'Event Berhasil Diperbarui!'], 200);
  }

}