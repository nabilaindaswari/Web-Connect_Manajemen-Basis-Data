<?php
require_once '../config/database.php';

try {
    $stmt = $conn->query("SELECT * FROM barang");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Gagal mengambil data: " . $e->getMessage();
    $data = []; // Bikin array kosong supaya tabel di bawahnya tidak error
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet">
    <title>Menu - Toko Sembako Indojaya</title>
    <style>
        /* Apple & Google Minimalist Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            /* Fallback to Apple system fonts if Google Sans fails */
            font-family: "Google Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #fbfbfd; /* Very subtle off-white ala Apple */
            color: #1d1d1f;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar Styling (Left Panel) */
        .sidebar {
            width: 260px;
            background-color: #ebebeb; /* Light gray from wireframe */
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            font-weight: 600;
            text-transform: lowercase;
            color: #333;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-group label {
            font-size: 0.9rem;
            color: #555;
            font-weight: 500;
        }

        .sort-select {
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            background-color: #fff;
            font-family: inherit;
            font-size: 0.9rem;
            outline: none;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .sort-select:focus {
            border-color: #0066cc; /* Apple link color for focus */
        }

        /* Main Content Styling (Right Panel) */
        .main-content {
            flex: 1;
            padding: 2rem 3rem;
            overflow-y: auto;
            position: relative;
        }

        /* Top Header Area */
        .top-bar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 2rem;
        }

        .btn-login {
            background-color: #e5e5ea;
            color: #1d1d1f;
            padding: 8px 24px;
            border-radius: 20px; /* Pill shape */
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }

        .btn-login:hover {
            background-color: #d1d1d6;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 2rem;
            padding-bottom: 3rem;
        }

        /* Minimalist Card Styling */
        .product-card {
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04); /* Soft shadow */
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        /* Image Placeholder Area */
        .card-image-area {
            height: 180px;
            background-color: #f5f5f7;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #86868b;
            font-size: 0.9rem;
            letter-spacing: 2px;
        }

        /* Image placeholder to simulate actual image tag if needed */
        .card-image-area img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Card Content Area */
        .card-content {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .card-category {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #86868b;
            margin-bottom: 0.4rem;
            font-weight: 600;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
            color: #1d1d1f;
            line-height: 1.3;
        }

        .card-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #f0f0f0;
        }

        .card-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1d1d1f;
        }

        /* Add to Transaction Button */
        .btn-add {
            background-color: #0066cc; /* Google/Apple primary blue */
            color: white;
            border: none;
            border-radius: 20px; /* Pill shape */
            padding: 6px 16px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
        }

        .btn-add:hover {
            background-color: #005bb5;
        }

        .btn-add:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>sort by</h2>
        
        <div class="filter-group">
            <label for="sort-price">Urutkan Harga</label>
            <select id="sort-price" class="sort-select">
                <option value="default">Pilih...</option>
                <option value="low">Termurah ke Termahal</option>
                <option value="high">Termahal ke Termurah</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="sort-category">Kategori</label>
            <select id="sort-category" class="sort-select">
                <option value="all">Semua Kategori</option>
                <option value="beras">Beras</option>
                <option value="minyak">Minyak Goreng</option>
                <option value="gula">Gula & Garam</option>
            </select>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <a href="login.php" class="btn-login">login</a>
        </div>

        <div class="product-grid">

            <!-- Looping data dari database -->
            <?php foreach ($data as $row): ?>
                <div class="product-card">
                    <div class="card-image-area">
                        foto
                    </div>
                    <div class="card-content">
                        <!-- Asumsi id_kategori bisa ditampilkan sementara -->
                        <div class="card-category">Kategori: <?= $row['id_kategori']; ?></div>
                        
                        <div class="card-title"><?= htmlspecialchars($row['nama_barang']); ?></div>
                        
                        <!-- Tambahan info stok (opsional, karena ada di database) -->
                        <div style="font-size: 0.8rem; color: #888; margin-bottom: 10px;">
                            Stok: <?= $row['stok']; ?>
                        </div>

                        <div class="card-footer">
                            <!-- Format angka ke format Rupiah -->
                            <div class="card-price">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div> <!-- Penutup product-grid -->
    </div> <!-- Penutup main-content -->

</body>
</html>