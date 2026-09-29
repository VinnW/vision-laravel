<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ConvertsToWebp;

class ComingUpNextController
{
    use ConvertsToWebp;

    private const TABLE = 'coming_up_next_section';


    /*
    |--------------------------------------------------------------------------
    | GET CONTENT
    |--------------------------------------------------------------------------
    */

    public function getContent()
    {
        /*
         * Karena Coming Up Next hanya mempunyai
         * satu konten, kita cukup mengambil satu row.
         *
         * Kalau tabel kosong:
         * hasil = null
         *
         * Kalau sudah ada:
         * hasil = object
         */

        $content = DB::table(self::TABLE)
            ->first();


        return response()->json(
            $content,
            200
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE CONTENT
    |--------------------------------------------------------------------------
    */

    public function createContent(Request $request)
    {
        /*
         * Jangan izinkan membuat konten kedua.
         */

        if (DB::table(self::TABLE)->exists()) {

            return response()->json([
                'message' => 'Konten sudah ada. Silakan ubah konten yang ada.'
            ], 409);

        }


        /*
         * Gambar wajib untuk konten baru.
         */

        if (!$request->hasFile('image_url')) {

            return response()->json([
                'message' => 'Gambar harus diunggah!'
            ], 400);

        }


        /*
         * Title wajib.
         */

        if (!$request->filled('title')) {

            return response()->json([
                'message' => 'Judul harus diisi!'
            ], 400);

        }


        /*
         * Description wajib.
         */

        if (!$request->filled('description')) {

            return response()->json([
                'message' => 'Deskripsi harus diisi!'
            ], 400);

        }


        /*
         * Convert gambar ke WEBP.
         */

        $image = $this->convertToWebp(
            $request->file('image_url')
        );


        /*
         * INSERT hanya dilakukan pada saat
         * user benar-benar menekan "Buat Konten".
         */

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


    /*
    |--------------------------------------------------------------------------
    | UPDATE CONTENT
    |--------------------------------------------------------------------------
    */

    public function updateContent(Request $request)
    {
        /*
         * Pastikan konten memang sudah ada.
         */

        if (!DB::table(self::TABLE)->exists()) {

            return response()->json([

                'message' =>
                    'Konten belum dibuat. Silakan buat konten terlebih dahulu.'

            ], 404);

        }


        /*
         * Tempat menyimpan perubahan.
         */

        $data = [];


        /*
         * Jika user memilih gambar baru,
         * convert ke WEBP.
         */

        if ($request->hasFile('image_url')) {

            $data['image_url'] =
                $this->convertToWebp(
                    $request->file('image_url')
                );

        }


        /*
         * Update title.
         */

        if ($request->filled('title')) {

            $data['title'] =
                $request->input('title');

        }


        /*
         * Update description.
         */

        if ($request->filled('description')) {

            $data['description'] =
                $request->input('description');

        }


        /*
         * Tidak ada data yang berubah.
         */

        if (empty($data)) {

            return response()->json([

                'message' =>
                    'Tidak ada perubahan untuk disimpan.'

            ], 400);

        }


        /*
         * Karena desain tabel ini memang hanya
         * menyimpan satu konten, update dilakukan
         * terhadap row yang ada.
         */

        DB::table(self::TABLE)
            ->update($data);


        return response()->json([

            'message' =>
                'Konten berhasil diubah!'

        ], 200);
    }
}