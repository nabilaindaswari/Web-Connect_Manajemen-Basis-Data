<?php
require_once '../config/database.php';
session_start();
/* ======================================================
   SESSION KERANJANG
====================================================== */

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}


/* ======================================================
   AMBIL DATA BARANG + KATEGORI
====================================================== */

$stmtBarangList = $pdo->prepare("
    SELECT 
        barang.id_barang,
        barang.nama_barang,
        barang.id_kategori,
        kategori.nama_kategori,
        barang.harga,
        barang.stok,
        barang.pict
    FROM barang
    JOIN kategori 
        ON barang.id_kategori = kategori.id_kategori
");

$stmtBarangList->execute();

$barang_list = $stmtBarangList->fetchAll(PDO::FETCH_ASSOC);


/* ======================================================
   AMBIL DATA KATEGORI
====================================================== */

$stmtKategori = $pdo->prepare("
    SELECT *
    FROM kategori
");

$stmtKategori->execute();

$kategori_list = $stmtKategori->fetchAll(PDO::FETCH_ASSOC);


/* ======================================================
   HITUNG TOTAL KERANJANG
====================================================== */

$total_keranjang = 0;
$jumlah_item = 0;

foreach ($_SESSION['keranjang'] as $item) {

    $subtotal = $item['harga'] * $item['jumlah_barang'];

    $total_keranjang += $subtotal;

    $jumlah_item += $item['jumlah_barang'];
}


/* ======================================================
   TAMBAHKAN qty_dipesan KE MASING-MASING BARANG
====================================================== */

foreach ($barang_list as &$barang) {

    $qty_dipesan = 0;

    foreach ($_SESSION['keranjang'] as $cart) {

        if ($cart['id_barang'] == $barang['id_barang']) {

            $qty_dipesan += $cart['jumlah_barang'];
        }
    }

    $barang['qty_dipesan'] = $qty_dipesan;
}

unset($barang);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- ## bagian konfigurasi meta dan font -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Toko Sembako Indojaya</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- ## styling CSS untuk dashboard -->
    <style>
        :root {
            --font-main: 'Google Sans', 'Inter', sans-serif;
            --bg-body: #E5D3B3;
            --sidebar-bg: #432E22;
            --sidebar-border: rgba(255, 255, 255, 0.2);
            --top-btn-bg: #A19A6C;
            --login-btn-bg: #715033;
            --card-top-bg: #E1CDBC;
            --card-bottom-bg: #9A9467;
            --bottom-bar-bg: #989267;
            --btn-lanjut-bg: #3F2921;
            --text-light: #ffffff;
            --text-dark: #333333;
            --border-radius-card: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-body);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            color: var(--text-light);
            z-index: 10;
        }

        .hamburger-menu {
            padding: 20px 30px;
            cursor: pointer;
        }

        .hamburger-menu div {
            width: 30px;
            height: 2px;
            background-color: var(--text-light);
            margin-bottom: 6px;
        }

        .sidebar-menu {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
        }

        .sidebar-item {
            padding: 15px 30px;
            font-size: 16px;
            color: var(--text-light);
            text-decoration: none;
            border-bottom: 1px solid var(--sidebar-border);
            border-top: 1px solid transparent;
            transition: background 0.2s;
        }
        
        .sidebar-item:first-child {
            border-top: 1px solid var(--sidebar-border);
        }

        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px 40px;
            padding-bottom: 120px; /* Space for bottom bar */
            overflow-y: auto;
            position: relative;
        }

        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .top-actions-left {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .select-sort {
            background-color: #EFE4D3;
            border: 1px solid #d4c6b3;
            padding: 10px 35px 10px 15px;
            border-radius: 6px;
            font-family: var(--font-main);
            font-size: 16px;
            color: var(--text-dark);
            outline: none;
            width: 180px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            cursor: pointer;
        }

        .btn-terapkan {
            background-color: var(--top-btn-bg);
            color: var(--text-dark);
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-family: var(--font-main);
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Scrollbar track for visual detail */
        .scroll-track {
            position: absolute;
            right: 15px;
            top: 100px;
            bottom: 120px;
            width: 4px;
            background-color: #432E22;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
            padding-right: 20px;
        }

        .

        /* Product Card */
        .product-card {
            background-color: transparent;
            border-radius: var(--border-radius-card);
            display: flex;
            flex-direction: column;
            height: 280px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.2s;
            border: none;
            text-align: left;
            padding: 0;
            width: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-4px);
        }

        .card-top {
            background-color: var(--card-top-bg);
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border-top-left-radius: var(--border-radius-card);
            border-top-right-radius: var(--border-radius-card);
        }

        .card-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #6B2D1D;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
        }
        .btn-kurang {
            position: absolute;
            top: 40px;
            right: 10px;
            background-color: #6B2D1D;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
        }


    .card-bottom {
            background-color: var(--card-bottom-bg);
            flex: 1;
            padding: 15px;
            color: var(--text-light);
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 100%;
            border-bottom-left-radius: var(--border-radius-card);
            border-bottom-right-radius: var(--border-radius-card);
            
            /* -- TAMBAHAN RESET BUTTON -- */
            border: none;          /* Menghilangkan garis pinggir bawaan tombol */
            text-align: left;      /* Memastikan teks rata kiri */
            cursor: pointer;       /* Mengubah kursor jadi tangan saat di-hover */
            font-family: inherit;  /* Menyesuaikan font dengan tema website */
        }
        .card-kategori {
            font-size: 11px;
            margin-bottom: 2px;
            font-family: var(--font-main);
        }

        .card-title {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 2px;
            font-family: var(--font-main);
        }

        .card-stok {
            font-size: 11px;
            margin-bottom: 5px;
            font-family: var(--font-main);
        }

        .card-price {
            font-size: 16px;
            font-weight: 500;
            font-family: var(--font-main);
        }

       
        .bottom-bar {
            position: fixed;
            bottom: 0;
            left: 250px;
            width: calc(100% - 250px);
            background-color: var(--bottom-bar-bg);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
        }

        .bottom-info {
            display: flex;
            gap: 50px;
            font-size: 24px;
            color: var(--text-light);
            font-family: var(--font-main);
        }

        .btn-lanjutkan {
            background-color: var(--btn-lanjut-bg);
            color: var(--text-light);
            text-decoration: none;
            padding: 12px 35px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 500;
            transition: opacity 0.3s;
        }

        .btn-lanjutkan:hover {
            opacity: 0.9;
        }

    </style>
</head>
<body>

    <!-- ## bagian navigasi sidebar menu -->
    <aside class="sidebar">
        <!-- ;tombol -> hamburger-menu sidebar -->
        <div class="hamburger-menu">
            <div></div>
            <div></div>
            <div></div>
        </div>
        
        <div class="sidebar-menu">
            <a href="#" class="sidebar-item">Semua(All)</a>
            <!-- ;connect -> looping data $kategori_list untuk menampilkan menu kategori -->
            <?php foreach($kategori_list as $kat): ?>
                <a href="#" class="sidebar-item"><?= htmlspecialchars($kat['nama_kategori']) ?></a>
            <?php endforeach; ?>
        </div>
    </aside>

    <!-- ## bagian utama konten dasboard kasir -->
    <main class="main-content">
        
        <!-- ## bagian atas konten (pencarian, urutan, & tombol aksi) -->
        <div class="top-bar">
            <div class="top-actions-left">
                
                <!-- ;tombol -> filter sort -->
                <select class="select-sort">
                    <option>Urutkan Harga</option>
                    <option>Termurah</option>
                    <option>Termahal</option>
                </select>
                
                <!-- ;tombol terapkan -> btn-terapkan  -->
                <button class="btn-terapkan">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    Terapkan
                </button>
                <!-- (Kasir tidak memiliki tombol Tambah Produk Baru) -->
            </div>
        </div>

        <div class="scroll-track"></div>

        <!-- ## bagian grid daftar produk -->
        <div class="product-grid">
            
            <!-- ;connect -> $barang_list, query nya join antara kategori dan barang -->
            <?php foreach($barang_list as $index => $barang): ?>

            <!-- KEMBALIKAN TAG FORM SEBAGAI PEMBUNGKUS UTAMA -->
            <!-- Aksi default form ini adalah ADD CART -->
            <form action="../process/add_cart.php" method="POST" style="margin:0;">
                
                <!-- ## bagian ini untuk perhitungan transaksi -->
                <input type="hidden" name="id_barang" value="<?= htmlspecialchars($barang['id_barang']) ?>">
                <input type="hidden" name="harga" value="<?= htmlspecialchars($barang['harga']) ?>">
                <input type="hidden" name="jumlah_barang" value="1">

                <div class="product-card">
                    
                    <!-- ini untuk bagian atas card, atau bagian gambarnya -->
                    <div class="card-top">
                        <?php if($barang['qty_dipesan'] > 0): ?>
                            <div class="card-badge"><?= $barang['qty_dipesan'] ?></div>
                            
                            <!-- Tombol ini MEMBAJAK form untuk mengarah ke KURANG CART -->
                            <button type="submit" class="btn-kurang" formaction="../process/kurang_cart.php" title="Kurangi 1">
                                -
                            </button>
                        <?php endif; ?>
                        
                        <img src="../public/menuPict/<?= htmlspecialchars($barang['pict']) ?>" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    
                    <!-- ## bagian bawah info di tiap card -->
                    <!-- Tombol ini akan mengeksekusi aksi default form (yaitu add_cart.php) -->
                    <button type="submit" class="card-bottom">
                        <div class="card-kategori">Kategori : <?= str_pad($barang['id_kategori'], 2, '0', STR_PAD_LEFT) ?></div>
                        <div class="card-title"><?= htmlspecialchars($barang['nama_barang']) ?></div>
                        <div class="card-stok">Stok : <?= htmlspecialchars($barang['stok']) ?></div>
                        <div class="card-price">Rp. <?= number_format($barang['harga'], 0, ',', '.') ?></div>
                    </button>
                    
                </div>
            </form>
            <?php endforeach; ?>
        </div>
    </main>

    <!--## tampilan bar bawah, bar bawah isinya detail yang udah di pilih -->
    <div class="bottom-bar">
        <div class="bottom-info">
            <!-- ;connect -> ambil dan tampilkan total nilai transaksi dari variable session/backend -->
            <span>Total Amount : Rp. <?= number_format($total_keranjang, 0, ',', '.') ?></span>
            
            <!-- ;connect -> ambil total jumlah item yang masuk keranjang -->
            <span>Total Barang : <?= $jumlah_item ?></span>
        </div>
        
        <!-- ;tombol lanjutkan checkout -> btn-lanjutkan -->
        <a href="checkout.php" class="btn-lanjutkan">Lanjutkan</a>
    </div>

</body>
</html>

```