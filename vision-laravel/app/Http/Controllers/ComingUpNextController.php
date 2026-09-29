<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ConvertsToWebp;

class ComingUpNextController
{
    use ConvertsToWebp;

    private const TABLE = 'coming_up_next_section';

    public function getContent()
    {

        $content = DB::table(self::TABLE)
            ->first();


        return response()->json(
            $content,
            200
        );
    }

    public function createContent(Request $request)
    {

        if (DB::table(self::TABLE)->exists()) {

            return response()->json([
                'message' => 'Konten sudah ada. Silakan ubah konten yang ada.'
            ], 409);

        }

        if (!$request->hasFile('image_url')) {

            return response()->json([
                'message' => 'Gambar harus diunggah!'
            ], 400);

        }

        if (!$request->filled('title')) {

            return response()->json([
                'message' => 'Judul harus diisi!'
            ], 400);

        }

        if (!$request->filled('description')) {

            return response()->json([
                'message' => 'Deskripsi harus diisi!'
            ], 400);

        }

        $image = $this->convertToWebp(
            $request->file('image_url')
        );

        DB::table(self::TABLE)->insert([

            'image_url' =>
                $image,

            'title' =>
                $request->input('title'),

            'description' =>
                $request->input('description'),

        ]);


        return response()->json([

            'message' =>
                'Konten berhasil dibuat!'

        ], 200);
    }

    public function updateContent(Request $request)
    {

        if (!DB::table(self::TABLE)->exists()) {

            return response()->json([

                'message' =>
                    'Konten belum dibuat. Silakan buat konten terlebih dahulu.'

            ], 404);

        }

        $data = [];

        if ($request->hasFile('image_url')) {

            $data['image_url'] =
                $this->convertToWebp(
                    $request->file('image_url')
                );

        }

        if ($request->filled('title')) {

            $data['title'] =
                $request->input('title');

        }

        if ($request->filled('description')) {

            $data['description'] =
                $request->input('description');

        }

        if (empty($data)) {

            return response()->json([

                'message' =>
                    'Tidak ada perubahan untuk disimpan.'

            ], 400);

        }

        DB::table(self::TABLE)
            ->update($data);


        return response()->json([

            'message' =>
                'Konten berhasil diubah!'

        ], 200);
    }
}