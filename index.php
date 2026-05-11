<?php
/**
 * Project: Aplikasi Pencarian Anime (Tugas Informatika)
 * Nama: Hana
 * API Source: Jikan API (MyAnimeList Unofficial)
 */

// --- BAGIAN 1: LOGIKA PEMROGRAMAN (BACKEND) ---

$searchQuery = isset($_GET['query']) ? $_GET['query'] : '';
$isSearching = !empty($searchQuery);

// Tentukan alamat API (Endpoint)
if ($isSearching) {
    // Jika user mencari judul tertentu
    $apiUrl = "https://api.jikan.moe/v4/anime?q=" . urlencode($searchQuery) . "&limit=12";
    $pageTitle = "Hasil Pencarian: " . htmlspecialchars($searchQuery);
} else {
    // Tampilan awal: Ambil anime terpopuler
    $apiUrl = "https://api.jikan.moe/v4/top/anime?limit=12";
    $pageTitle = "Anime Terpopuler";
}

// Pengaturan koneksi (Agar tidak error saat akses HTTPS dari localhost)
$options = [
    "http" => ["header" => "User-Agent: HanaProject/1.0\r\n"],
    "ssl"  => ["verify_peer" => false, "verify_peer_name" => false]
];

// Proses pengambilan data dari internet
$context  = stream_context_create($options);
$response = @file_get_contents($apiUrl, false, $context);

// Ubah format JSON menjadi array PHP
$result = json_decode($response, true);
$animeList = isset($result['data']) ? $result['data'] : [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hana Anime DB</title>
    
    <!-- Memakai Bootstrap CSS untuk tampilan kartu dan layout -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero-section { background: #2c3e50; color: white; padding: 60px 0; margin-bottom: -30px; }
        .search-card { border-radius: 50px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .anime-card { 
            border: none; 
            border-radius: 15px; 
            overflow: hidden; 
            transition: transform 0.3s ease; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .anime-card:hover { transform: translateY(-10px); }
        .card-img-top { height: 320px; object-fit: cover; }
        .score-badge { 
            position: absolute; top: 10px; right: 10px; 
            background: rgba(255, 193, 7, 0.95); 
            font-weight: bold; padding: 5px 12px; border-radius: 20px;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="hero-section text-center">
    <div class="container">
        <h1 class="fw-bold">⛩️ HANA ANIME DATABASE</h1>
        <p class="lead">Tugas Integrasi API - Teknik Informatika</p>
    </div>
</div>

<div class="container">
    <!-- Form Pencarian -->
    <div class="row justify-content-center">
        <div class="col-md-7">
            <form action="" method="GET">
                <div class="input-group search-card overflow-hidden">
                    <input type="text" name="query" class="form-control p-3 border-0" 
                           placeholder="Ketik judul anime (contoh: Solo Leveling)..." 
                           value="<?= htmlspecialchars($searchQuery) ?>" required>
                    <button class="btn btn-primary px-4" type="submit">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Judul Halaman -->
    <h3 class="mt-5 mb-4 text-dark fw-bold"><?= $pageTitle ?></h3>

    <!-- List Data Anime -->
    <div class="row">
        <?php if (!empty($animeList)): ?>
            <?php foreach ($animeList as $anime): ?>
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="card anime-card h-100">
                        <!-- Badge Skor -->
                        <div class="score-badge">⭐ <?= $anime['score'] ?? '0' ?></div>
                        
                        <img src="<?= $anime['images']['jpg']['image_url'] ?>" class="card-img-top" alt="Poster">
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title fw-bold text-truncate" title="<?= $anime['title'] ?>">
                                <?= $anime['title'] ?>
                            </h6>
                            <p class="small text-muted mb-3">
                                <?= $anime['type'] ?> • <?= $anime['episodes'] ?? '?' ?> Eps
                            </p>
                            <div class="mt-auto">
                                <a href="<?= $anime['url'] ?>" target="_blank" class="btn btn-sm btn-outline-primary w-100 rounded-pill">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Anime tidak ditemukan. Periksa koneksi internet atau coba kata kunci lain.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="text-center py-5 mt-5 text-muted border-top bg-white">
    <p>&copy; 2026 Hana - Mahasiswa Informatika</p>
</footer>

</body>
</html>