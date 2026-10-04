<?php

/*
|--------------------------------------------------------------------------
| Data Produk Layanan
|--------------------------------------------------------------------------
| Sumber data tunggal untuk:
|   - products.blade.php         (slider: name, description, image)
|   - detail-products.blade.php  (halaman detail: + details, catalog)
|
| image   : path relatif terhadap public/ (dibungkus asset() di view)
| details : paragraf panjang untuk halaman detail
| catalog : URL file katalog (kosongkan untuk menyembunyikan tombol)
*/

return [
    [
        'slug'        => 'asuransi-kesehatan',
        'name'        => 'Asuransi Kesehatan',
        'description' => 'Perlindungan kesehatan untuk membantu memberikan rasa aman dalam menghadapi kebutuhan medis dan biaya perawatan.',
        'image'       => 'storage/image_products/asuransi-kesehatan.jpg',
        'catalog'     => '#',
        'details'     => [
            'Perlindungan kesehatan untuk membantu memberikan rasa aman dalam menghadapi kebutuhan medis dan biaya perawatan. Produk ini dirancang agar Anda dan keluarga dapat fokus pada pemulihan tanpa terbebani biaya yang tidak terduga.',
            'Manfaat perlindungan mencakup rawat inap, rawat jalan, tindakan medis, hingga santunan tertentu sesuai dengan paket yang dipilih. Ganti paragraf ini dengan penjelasan manfaat yang sebenarnya.',
            'Hubungi tim kami untuk mendapatkan simulasi premi dan rekomendasi paket yang paling sesuai dengan kebutuhan Anda.',
        ],
    ],
    [
        'slug'        => 'asuransi-kendaraan',
        'name'        => 'Asuransi Kendaraan',
        'description' => 'Perlindungan kendaraan dari berbagai risiko sehingga Anda dapat berkendara dengan lebih tenang.',
        'image'       => 'storage/image_products/asuransi-kendaraan.jpg',
        'catalog'     => '#',
        'details'     => [
            'Perlindungan kendaraan dari berbagai risiko sehingga Anda dapat berkendara dengan lebih tenang. Tersedia pilihan perlindungan menyeluruh maupun risiko sebagian sesuai kebutuhan.',
            'Risiko yang dapat ditanggung meliputi kecelakaan, kehilangan akibat pencurian, kebakaran, hingga bencana alam. Ganti paragraf ini dengan penjelasan manfaat yang sebenarnya.',
            'Proses klaim dibuat sederhana dengan dukungan jaringan bengkel rekanan yang luas.',
        ],
    ],
    [
        'slug'        => 'asuransi-jiwa',
        'name'        => 'Asuransi Jiwa',
        'description' => 'Perlindungan finansial bagi Anda dan keluarga untuk menghadapi berbagai risiko kehidupan.',
        'image'       => 'storage/image_products/asuransi-jiwa.jpg',
        'catalog'     => '#',
        'details'     => [
            'Perlindungan finansial bagi Anda dan keluarga untuk menghadapi berbagai risiko kehidupan. Memastikan masa depan orang-orang tersayang tetap terjaga.',
            'Pilih manfaat yang sesuai, mulai dari proteksi jiwa murni hingga kombinasi dengan nilai investasi. Ganti paragraf ini dengan penjelasan manfaat yang sebenarnya.',
            'Konsultasikan kebutuhan uang pertanggungan Anda bersama tim kami.',
        ],
    ],
    [
        'slug'        => 'asuransi-properti',
        'name'        => 'Asuransi Properti',
        'description' => 'Perlindungan untuk rumah dan aset properti dari berbagai risiko yang tidak terduga.',
        'image'       => 'storage/image_products/asuransi-properti.jpg',
        'catalog'     => '#',
        'details'     => [
            'Perlindungan untuk rumah dan aset properti dari berbagai risiko yang tidak terduga, seperti kebakaran, banjir, gempa bumi, dan pencurian.',
            'Perlindungan dapat diperluas hingga isi bangunan dan tanggung jawab hukum kepada pihak ketiga. Ganti paragraf ini dengan penjelasan manfaat yang sebenarnya.',
            'Kami membantu menghitung nilai pertanggungan yang tepat agar perlindungan tidak kurang maupun berlebih.',
        ],
    ],
    [
        'slug'        => 'asuransi-perjalanan',
        'name'        => 'Asuransi Perjalanan',
        'description' => 'Perlindungan perjalanan untuk membantu Anda menghadapi berbagai risiko selama bepergian.',
        'image'       => 'storage/image_products/asuransi-perjalanan.jpg',
        'catalog'     => '#',
        'details'     => [
            'Perlindungan perjalanan untuk membantu Anda menghadapi berbagai risiko selama bepergian, baik perjalanan dalam negeri maupun ke luar negeri.',
            'Mencakup pembatalan atau keterlambatan perjalanan, kehilangan bagasi, hingga biaya medis darurat di tempat tujuan. Ganti paragraf ini dengan penjelasan manfaat yang sebenarnya.',
            'Daftar sebelum berangkat agar seluruh rangkaian perjalanan Anda terlindungi.',
        ],
    ],
];