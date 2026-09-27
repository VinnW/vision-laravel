<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ConvertsToWebp;

class HomeSectionController
{
    use ConvertsToWebp;

    public function getBanners()
    {
        $result = DB::table('home_section')
            ->orderBy('id')
            ->get();

        return response()->json($result, 200);
    }

    public function createBanner(Request $request)
    {
        if (!$request->hasFile('banner_url')) {
            return response()->json([
                'message' => 'Gambar Harus Diunggah!'
            ], 400);
        }

        $banner = $this->convertToWebp(
            $request->file('banner_url')
        );

        $id = DB::table('home_section')->insertGetId([
            'banner_url' => $banner
        ]);

        return response()->json([
            'id' => $id,
            'message' => 'Gambar berhasil diunggah!',
            'image_url' => $banner
        ], 200);
    }

    public function updateBanner(Request $request, int $id)
    {
        if (!$request->hasFile('banner_url')) {
            return response()->json([
                'message' => 'Gambar Harus Diunggah!'
            ], 400);
        }

        $banner = $this->convertToWebp(
            $request->file('banner_url')
        );

        $updated = DB::table('home_section')
            ->where('id', $id)
            ->update([
                'banner_url' => $banner
            ]);

        return response()->json([
            'message' => 'Gambar Berhasil Diperbarui!',
            'updated_banner' => $updated,
            'image_url' => $banner
        ], 200);
    }

    public function deleteBanner(int $id)
    {
        $deleted = DB::table('home_section')
            ->where('id', $id)
            ->delete();

        return response()->json([
            'message' => 'Banner Berhasil Dihapus!',
            'deleted' => $deleted
        ], 200);
    }
}