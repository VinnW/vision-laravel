<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ConvertsToWebp;
use Intervention\Image\Length;

class ProductsController {

    use ConvertsToWebp;

    public function getProducts(){
        $result = DB::table('products')->get();
        return response()->json($result, 200);
    }

    public function createProducts(Request $request){

        if(!$request->hasFile('image_url_left')){
            return response()->json(['message' => "Gambar banner kiri harus diunggah!"]);
        }
        if(!$request->hasfile('image_url_top')){
            return response()->json(['message' => "Gambar banner atas harus diunggah!"]);
        }
        if(!$request->filled('text_title')){
            return response()->json(['message' => "Text Judul harus diisi!"]);
        }
        if(!$request->filled('text_desc')){
            return response()->json(['message' => "Text deskripsi harus diisi!"]);
        }

        $image_left = $this->convertToWebp($request->file('inmage_url_left'));
        $image_top = $this->convertToWebp($request->file('image_url_top'));
        $text_title = $request->input('text_title');
        $text_desc = $request->input('text_desc');

        $result = DB::table('products')->insert([
            'image_url_left' => $image_left,
            'image_url_top' => $image_top,
            'text_title' => $text_title,
            'text_desc' => $text_desc
        ]);

        return response()->json($result, 200);

    }

    public function editProducts(Request $request){
        
        $toUpdate = [];

        if($request->hasfile('image_url_left')){
            $image_left = $this->convertToWebp($request->file('inmage_url_left'));
            $toUpdate['image_url_left'] = $image_left;
        }
        if($request->hasfile('image_url_top')){
            $image_top = $this->convertToWebp($request->file('inmage_url_top'));
            $toUpdate['image_url_left'] = $image_top;
        }
        if($request->filled('text_title')){
            $text_title = $request->input('text_title');
            $toUpdate['text_title'] = $text_title;
        }
        if($request->filled('text_desc')){
            $text_desc = $request->input('text_desc');
            $toUpdate['text_desc'] = $text_desc;
        }
        

        if(count($toUpdate) === 0){
            return response()->json('Tidak ada data yang diperbarui!', 400);
        }else{
            $result = DB::table('products')->update($toUpdate);
            return response()->json($result, 200);
        }

    }

}