<?php
require_once '../process/proses_kasir_home.php';

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
        /* =========================================
        CSS ERROR POP-OUT MODAL
        ========================================= */
        .error-modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
        }

        .error-modal-overlay.active {
            opacity: 1; visibility: visible;
        }

        .error-card {
            background-color: #ffffff;
            width: 100%; max-width: 380px;
            padding: 35px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .error-modal-overlay.active .error-card {
            transform: translateY(0);
        }

        .error-icon-wrapper {
            width: 64px; height: 64px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }

        .error-card h2 {
            font-size: 20px; font-weight: 600; margin-bottom: 10px; color: #333;
        }

        .error-card p {
            font-size: 14px; color: #666; line-height: 1.5; margin-bottom: 25px;
        }

        .error-btn-close {
            display: inline-block; width: 100%;
            background-color: var(--btn-lanjut-bg);
            color: #ffffff; border: none;
            padding: 12px 0; border-radius: 8px;
            font-size: 15px; font-weight: 500;
            cursor: pointer; transition: opacity 0.2s;
            font-family: var(--font-main);
        }

        .error-btn-close:hover { opacity: 0.9; }

        /* =========================================
        CSS TOMBOL LOGOUT & MODAL BUTTONS
        ========================================= */
        .btn-logout {
            background-color: #8B3A3A; /* Warna merah gelap agar berbeda dari tombol aksi lain */
            color: var(--text-light);
            text-decoration: none;
            padding: 10px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: opacity 0.2s;
            cursor: pointer;
            font-family: var(--font-main);
        }

        .btn-logout:hover {
            opacity: 0.9;
        }

        .modal-action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn-batal {
            flex: 1;
            background-color: #EFE4D3;
            color: var(--text-dark);
            border: 1px solid #d4c6b3;
            padding: 12px 0;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
            font-family: var(--font-main);
        }

        .btn-yakin-keluar {
            flex: 1;
            background-color: #dd6b20; /* Warna orange yang sama dengan icon warning */
            color: #ffffff;
            border: none;
            padding: 12px 0;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.2s;
            font-family: var(--font-main);
        }

        .btn-batal:hover, .btn-yakin-keluar:hover {
            opacity: 0.8;
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
            <a href="?" class="sidebar-item <?= empty($_GET['kategori']) ? 'selected' : '' ?>">Semua (All)</a>
            <!-- ;connect -> looping data $kategori_list untuk menampilkan menu kategori -->
            <?php foreach($kategori_list as $kat): ?>
                <!-- Memasukkan id_kategori ke dalam URL -->
                <a href="?kategori=<?= $kat['id_kategori'] ?>" 
                class="sidebar-item <?= (isset($_GET['kategori']) && $_GET['kategori'] == $kat['id_kategori']) ? 'selected' : '' ?>">
                    <!-- Menampilkan nama kategori -->
                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </aside>

    <!-- ## bagian utama konten dasboard kasir -->
    <main class="main-content">
        
        <!-- ## bagian atas konten (pencarian, urutan, & tombol aksi) -->
        <div class="top-bar">
            <div class="top-actions-left">
                
                <!-- ;tombol -> filter sort -->
            <form method="GET" style="display:flex; gap:15px; align-items:center;">
                <input type="hidden" 
                    name="kategori" 
                    value="<?= htmlspecialchars($_GET['kategori'] ?? '') ?>">
                    
                <select name="sort" class="select-sort">

                    <option value="">Urutkan Harga</option>

                    <option value="termurah"
                        <?= ($sort === 'termurah') ? 'selected' : '' ?>>
                        Termurah
                    </option>

                    <option value="termahal"
                        <?= ($sort === 'termahal') ? 'selected' : '' ?>>
                        Termahal
                    </option>

                </select>

                <button type="submit" class="btn-terapkan">
                    <svg width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>

                    Terapkan
                </button>

            </form>
                <!-- (Kasir tidak memiliki tombol Tambah Produk Baru) -->
            </div>
            <button type="button" class="btn-logout" onclick="openLogoutModal()">Log Out</button>
        </div>

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
                    <button type="submit" class="card-bottom" onclick="return checkStock(event, <?= $barang['stok'] ?>, <?= $barang['qty_dipesan'] ?>)">
                        <div class="card-kategori">Kategori : <?= htmlspecialchars($barang['nama_kategori']) ?></div>
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
    <div class="error-modal-overlay" id="errorModalOverlay">
        <div class="error-card">
            <div class="error-icon-wrapper" id="errorIconWrapper"></div>
            <h2 id="errorTitle">Judul Error</h2>
            <p id="errorMessage">Pesan error di sini.</p>
            <button type="button" class="error-btn-close" onclick="closeErrorModal()">Mengerti</button>
        </div>
    </div>
    <div class="error-modal-overlay" id="logoutModalOverlay">
        <div class="error-card">
            <div class="error-icon-wrapper" style="background-color: #fffaf0; color: #dd6b20;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            
            <h2>Konfirmasi Log Out</h2>
            <p style="margin-bottom: 10px;">Apakah Anda yakin ingin keluar dari sistem?</p>
            
            <div class="modal-action-buttons">
                <button type="button" class="btn-batal" onclick="closeLogoutModal()">Batal</button>
                <a href="../process/logout.php" class="btn-yakin-keluar">Ya, Keluar</a>
            </div>
        </div>
    </div>
    <script>
        function checkStock(event, stok, qtyDipesan) {
            // Konversi ke tipe data angka
            stok = parseInt(stok);
            qtyDipesan = parseInt(qtyDipesan);

            if (stok === 0) {
                // Error 2: Stok sudah habis total di database
                event.preventDefault(); 
                showError('habis');
                return false;
            } else if (qtyDipesan >= stok) {
                // Error 1: Stok di keranjang sudah mencapai batas stok database
                event.preventDefault(); 
                showError('kurang');
                return false;
            }
            
            // Jika aman, lanjutkan proses submit ke add_cart.php
            return true; 
        }

        function showError(type) {
            const modal = document.getElementById('errorModalOverlay');
            const title = document.getElementById('errorTitle');
            const msg = document.getElementById('errorMessage');
            const iconWrap = document.getElementById('errorIconWrapper');

            if (type === 'habis') {
                title.innerText = 'Stok Habis!';
                msg.innerText = 'Maaf, barang ini sedang kosong dan tidak dapat ditambahkan ke keranjang.';
                iconWrap.style.backgroundColor = '#fdf2f2'; // Merah muda pudar
                iconWrap.style.color = '#e53e3e';           // Merah X
                iconWrap.innerHTML = `<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`;
            } else if (type === 'kurang') {
                title.innerText = 'Stok Tidak Mencukupi!';
                msg.innerText = 'Anda sudah memasukkan semua ketersediaan stok barang ini ke dalam keranjang.';
                iconWrap.style.backgroundColor = '#fffaf0'; // Orange muda pudar
                iconWrap.style.color = '#dd6b20';           // Orange !
                iconWrap.innerHTML = `<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`;
            }

            modal.classList.add('active');
        }

        function closeErrorModal() {
            document.getElementById('errorModalOverlay').classList.remove('active');
        }
        function openLogoutModal() {
            document.getElementById('logoutModalOverlay').classList.add('active');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModalOverlay').classList.remove('active');
        }

        // Menutup modal jika area gelap di luar card diklik
        window.addEventListener('click', function(e) {
            const logoutOverlay = document.getElementById('logoutModalOverlay');
            if (e.target === logoutOverlay) {
                closeLogoutModal();
            }
        });
    </script>
</body>
</html>

```