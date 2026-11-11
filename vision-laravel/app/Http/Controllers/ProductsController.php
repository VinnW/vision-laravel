<?php

namespace App\Http\Controllers;

use App\Traits\ConvertsToWebp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ProductsController
{
    use ConvertsToWebp;

    /**
     * Folder penyimpanan katalog PDF di disk "public"
     * (storage/app/public/catalogs → diakses lewat route download, bukan link langsung).
     */
    private const CATALOG_DIR = 'catalogs';

    /*
    |--------------------------------------------------------------------------
    | ADMIN — dipakai oleh admin/sections/products-panel.blade.php
    |--------------------------------------------------------------------------
    */

    /**
     * GET /visionasurance/products
     * Semua produk, urut dari yang paling lama (urutan sama dengan slider di beranda).
     */
    public function getProducts()
    {
        $result = DB::table('products')->orderBy('id')->get();

        return response()->json($result, 200);
    }

    /**
     * POST /visionasurance/create-products
     * Field: image_url_left, image_url_top, text_title, text_desc, catalog_file (opsional, PDF)
     */
    public function createProducts(Request $request)
    {
        $request->validate([
            'image_url_left' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
            'image_url_top'  => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
            'text_title'     => 'required|string|max:255',
            'text_desc'      => 'required|string',
            'catalog_file'   => 'nullable|file|mimes:pdf|max:20480',
        ], $this->messages());

        $title = trim($request->input('text_title'));

        $data = [
            'image_url_left' => $this->convertToWebp($request->file('image_url_left')),
            'image_url_top'  => $this->convertToWebp($request->file('image_url_top')),
            'text_title'     => $title,
            'text_desc'      => $request->input('text_desc'),
            'catalog_url'    => null,
            'catalog_name'   => null,
        ];

        if ($request->hasFile('catalog_file')) {
            $data = array_merge($data, $this->storeCatalog($request->file('catalog_file'), $title));
        }

        $id = DB::table('products')->insertGetId($data);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan.',
            'id'      => $id,
        ], 201);
    }

    /**
     * POST /visionasurance/update-products
     * Field: id (wajib) + field apa saja yang ingin diubah.
     * Katalog baru otomatis menggantikan yang lama. Kirim remove_catalog=1 untuk menghapus katalog.
     */
    public function editProducts(Request $request)
    {
        $request->validate([
            'id'             => 'required|integer|exists:products,id',
            'image_url_left' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'image_url_top'  => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'text_title'     => 'nullable|string|max:255',
            'text_desc'      => 'nullable|string',
            'catalog_file'   => 'nullable|file|mimes:pdf|max:20480',
        ], $this->messages());

        $id      = (int) $request->input('id');
        $product = DB::table('products')->where('id', $id)->first();

        $toUpdate = [];

        if ($request->hasFile('image_url_left')) {
            $this->deleteFile($product->image_url_left);
            $toUpdate['image_url_left'] = $this->convertToWebp($request->file('image_url_left'));
        }

        if ($request->hasFile('image_url_top')) {
            $this->deleteFile($product->image_url_top);
            $toUpdate['image_url_top'] = $this->convertToWebp($request->file('image_url_top'));
        }

        if ($request->filled('text_title')) {
            $toUpdate['text_title'] = trim($request->input('text_title'));
        }

        if ($request->filled('text_desc')) {
            $toUpdate['text_desc'] = $request->input('text_desc');
        }

        // Ganti katalog ATAU hapus katalog
        if ($request->hasFile('catalog_file')) {
            $this->deleteFile($product->catalog_url);

            $title    = $toUpdate['text_title'] ?? $product->text_title;
            $toUpdate = array_merge($toUpdate, $this->storeCatalog($request->file('catalog_file'), $title));
        } elseif ($request->boolean('remove_catalog')) {
            $this->deleteFile($product->catalog_url);

            $toUpdate['catalog_url']  = null;
            $toUpdate['catalog_name'] = null;
        }

        if (count($toUpdate) === 0) {
            return response()->json(['message' => 'Tidak ada data yang diperbarui!'], 422);
        }

        DB::table('products')->where('id', $id)->update($toUpdate);

        return response()->json(['message' => 'Produk berhasil diperbarui.'], 200);
    }

    /**
     * POST /visionasurance/delete-products
     * Field: id. File gambar & katalog milik produk ikut dihapus.
     */
    public function deleteProducts(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:products,id',
        ], $this->messages());

        $id      = (int) $request->input('id');
        $product = DB::table('products')->where('id', $id)->first();

        $this->deleteFile($product->image_url_left);
        $this->deleteFile($product->image_url_top);
        $this->deleteFile($product->catalog_url);

        DB::table('products')->where('id', $id)->delete();

        return response()->json(['message' => 'Produk berhasil dihapus.'], 200);
    }


    /*
    |--------------------------------------------------------------------------
    | PUBLIK — halaman detail & unduh katalog
    |--------------------------------------------------------------------------
    */

    /**
     * GET /produk/{id?}
     * Menampilkan HANYA produk yang dipilih. Tanpa {id} → produk pertama.
     */
    public function showDetail($id = null)
    {
        if ($id !== null) {
            $product = DB::table('products')->where('id', $id)->first();
            abort_if(!$product, 404);
        } else {
            $product = DB::table('products')->orderBy('id')->first();
        }

        return view('public.sections.detail-products', [
            'product'    => $product,
            'standalone' => true,
        ]);
    }

    /**
     * GET /produk/{id}/katalog
     * Mengunduh katalog PDF terbaru milik produk (selalu file yang sedang aktif).
     */
    public function downloadCatalog(int $id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        abort_if(!$product || empty($product->catalog_url), 404);

        $path = $this->normalizePath($product->catalog_url);

        abort_unless(Storage::disk('public')->exists($path), 404);

        $name = trim((string) $product->catalog_name);

        if ($name === '') {
            $name = Str::slug($product->text_title ?: 'katalog') . '.pdf';
        }

        return response()->download(
            Storage::disk('public')->path($path),
            $name
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    /** Simpan PDF ke storage/app/public/catalogs, kembalikan kolom untuk database. */
    private function storeCatalog(UploadedFile $file, string $title): array
    {
        $filename = Str::slug($title ?: 'katalog') . '-' . time() . '.pdf';
        $path     = $file->storeAs(self::CATALOG_DIR, $filename, 'public');

        return [
            'catalog_url'  => $path,
            'catalog_name' => $file->getClientOriginalName(),
        ];
    }

    /** Hapus file lama dari disk "public" (aman bila file tidak ada / berupa URL luar). */
    private function deleteFile(?string $path): void
    {
        $path = trim((string) $path);

        if ($path === '' || preg_match('#^https?://#', $path)) {
            return;
        }

        try {
            Storage::disk('public')->delete($this->normalizePath($path));
        } catch (\Throwable $e) {
            // abaikan: file lama yang gagal dihapus tidak boleh menggagalkan proses simpan
        }
    }

    /** "storage/catalogs/x.pdf" atau "/catalogs/x.pdf" → "catalogs/x.pdf" */
    private function normalizePath(string $path): string
    {
        return ltrim(preg_replace('#^/?storage/#', '', trim($path)), '/');
    }

    private function messages(): array
    {
        return [
            'id.required'              => 'ID produk tidak ditemukan.',
            'id.exists'                => 'Produk tidak ditemukan.',
            'image_url_left.required'  => 'Gambar banner kiri harus diunggah!',
            'image_url_left.mimes'     => 'Banner kiri harus berformat JPG, PNG, atau WEBP.',
            'image_url_left.max'       => 'Ukuran banner kiri maksimal 10 MB.',
            'image_url_top.required'   => 'Gambar banner atas harus diunggah!',
            'image_url_top.mimes'      => 'Banner atas harus berformat JPG, PNG, atau WEBP.',
            'image_url_top.max'        => 'Ukuran banner atas maksimal 10 MB.',
            'text_title.required'      => 'Text Judul harus diisi!',
            'text_title.max'           => 'Judul maksimal 255 karakter.',
            'text_desc.required'       => 'Text deskripsi harus diisi!',
            'catalog_file.mimes'       => 'Katalog harus berupa file PDF.',
            'catalog_file.max'         => 'Ukuran katalog maksimal 20 MB.',
            'catalog_file.uploaded'    => 'Katalog gagal diunggah. Periksa batas upload_max_filesize di server.',
        ];
    }
}