<?php
# session_start();
require_once '../process/proses_admin_home.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Toko Sembako Indojaya</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

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
            --btn-edit-bg: #612B1F;
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

        /* =========================================
        CSS USER PROFILE (SIDEBAR)
        ========================================= */
        .user-profile {
            padding: 30px 30px 20px 30px;
            border-bottom: 1px solid var(--sidebar-border);
            margin-bottom: 10px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .user-profile .greeting-time {
            font-size: 21px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 2px;
            font-family: var(--font-main);
        }

        .user-profile .greeting-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-light);
            line-height: 1.3;
            font-family: var(--font-main);
        }

        .user-profile .role {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6); /* Warna putih yang memudar/transparan */
            font-weight: 400;
            font-family: var(--font-main);
            letter-spacing: 0.5px;
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

        .sidebar-item.selected {
            background-color: rgba(255, 255, 255, 0.1);
            font-weight: 600;
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
            background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            cursor: pointer;
        }

        .btn-terapkan, .btn-tambah {
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
            text-decoration: none;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
            padding-right: 20px;
        }

        /* Product Card */
        .product-card {
            background-color: transparent;
            border-radius: var(--border-radius-card);
            display: flex;
            flex-direction: column;
            height: 280px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-decoration: none;
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
            overflow: hidden;
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
            z-index: 2;
        }

        .btn-kurang {
            position: absolute;
            top: 44px;
            right: 10px;
            background-color: #6B2D1D;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            z-index: 2;
        }

        .card-bottom-wrapper {
            background-color: var(--card-bottom-bg);
            flex: 1;
            padding: 12px 15px 15px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            border-bottom-left-radius: var(--border-radius-card);
            border-bottom-right-radius: var(--border-radius-card);
        }

        .btn-edit-card {
            align-self: flex-start;
            margin-bottom: 6px;
            background-color: var(--btn-edit-bg);
            color: white;
            border: none;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 11px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
            font-family: inherit;
        }

        .card-submit-area {
            background: transparent;
            border: none;
            color: var(--text-light);
            text-align: left;
            cursor: pointer;
            font-family: inherit;
            padding: 0;
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .card-kategori { font-size: 11px; margin-bottom: 2px; font-family: var(--font-main); }
        .card-title    { font-size: 16px; font-weight: 500; margin-bottom: 2px; font-family: var(--font-main); }
        .card-stok     { font-size: 11px; margin-bottom: 5px; font-family: var(--font-main); }
        .card-price    { font-size: 16px; font-weight: 500; font-family: var(--font-main); }

        /* Bottom Bar */
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

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 200;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
        }
        
        .modal-overlay.active { opacity: 1; visibility: visible; }

        .modal-box {
            background-color: var(--bg-body);
            border-radius: 12px;
            padding: 30px;
            width: 100%;
            max-width: 450px;
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 15px; right: 15px;
            font-size: 22px;
            cursor: pointer;
            border: none;
            background: none;
            color: var(--sidebar-bg);
            line-height: 1;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .form-group label { 
            font-size: 13px; margin-bottom: 5px; color: #555; font-weight: 500; 
        }

        .form-control {
            padding: 10px;
            border: 1px solid var(--top-btn-bg);
            border-radius: 8px;
            background-color: #EFE4D3;
            font-family: var(--font-main);
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            padding-right: 35px;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            background-color: var(--sidebar-bg);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            font-family: var(--font-main);
        }
        
        .btn-submit:hover { opacity: 0.9; }

        /* Tambahan CSS Untuk Warning Input */
        .warning-text {
            color: #d93025;
            font-size: 11.5px;
            margin-top: 5px;
            font-weight: 500;
            display: none; /* Disembunyikan secara default */
        }

        .input-error {
            color: #d93025 !important;
            border-color: #d93025 !important;
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

    <aside class="sidebar">
        <div class="user-profile">
            <div class="greeting-time"><?= $sapaan ?></div>
            <div class="greeting-name"><?= htmlspecialchars($_SESSION['nama_kasir'] ?? 'User') ?></div>
            <div class="role">
                <?= (isset($_SESSION['access_level']) && $_SESSION['access_level'] >= 11) ? 'Admin' : 'Kasir' ?>
            </div>
        </div>
        
        <div class="sidebar-menu">
            <a href="?" class="sidebar-item <?= empty($_GET['kategori']) ? 'selected' : '' ?>">Semua (All)</a>
            
            <?php foreach($kategori_list as $kat): ?>
                <a href="?kategori=<?= $kat['id_kategori'] ?>"
                   class="sidebar-item <?= (isset($_GET['kategori']) && $_GET['kategori'] == $kat['id_kategori']) ? 'selected' : '' ?>">
                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="top-actions-left">
                <form method="GET" style="display:flex; gap:15px; align-items:center;">
                    <input type="hidden" name="kategori" value="<?= htmlspecialchars($_GET['kategori'] ?? '') ?>">
                    <select name="sort" class="select-sort">
                        <option value="">Urutkan Harga</option>
                        <option value="termurah" <?= ($sort === 'termurah') ? 'selected' : '' ?>>Termurah</option>
                        <option value="termahal" <?= ($sort === 'termahal') ? 'selected' : '' ?>>Termahal</option>
                    </select>
                    <button type="submit" class="btn-terapkan">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                        Terapkan
                    </button>
                </form>

                <button type="button" class="btn-tambah" id="btnOpenAdd">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    Tambahkan Produk Baru
                </button>
            </div>
            <button type="button" class="btn-logout" onclick="openLogoutModal()">Log Out</button>
        </div>

        <div class="product-grid">
            <?php foreach($barang_list as $barang): ?>
            <form action="../process/add_cart.php" method="POST" style="margin:0;">
                <input type="hidden" name="id_barang" value="<?= htmlspecialchars($barang['id_barang']) ?>">
                <input type="hidden" name="harga" value="<?= htmlspecialchars($barang['harga']) ?>">
                <input type="hidden" name="jumlah_barang" value="1">

                <div class="product-card">
                    <div class="card-top">
                        <?php if($barang['qty_dipesan'] > 0): ?>
                            <div class="card-badge"><?= $barang['qty_dipesan'] ?></div>
                            <button type="submit" class="btn-kurang"
                                    formaction="../process/kurang_cart.php"
                                    title="Kurangi 1">−</button>
                        <?php endif; ?>
                        <img src="../public/menuPict/<?= htmlspecialchars($barang['pict']) ?>"
                             style="width:100%; height:100%; object-fit:cover;"
                             alt="<?= htmlspecialchars($barang['nama_barang']) ?>">
                    </div>

                    <div class="card-bottom-wrapper">
                        <button type="button" class="btn-edit-card"
                                onclick="openEditModal(event, <?= (int)$barang['id_barang'] ?>)">
                            edit
                        </button>
                        <button type="submit" class="card-submit-area" onclick="return checkStock(event, <?= $barang['stok'] ?>, <?= $barang['qty_dipesan'] ?>)">
                            <div class="card-kategori">Kategori : <?= htmlspecialchars($barang['nama_kategori']) ?></div>
                            <div class="card-title"><?= htmlspecialchars($barang['nama_barang']) ?></div>
                            <div class="card-stok">Stok : <?= htmlspecialchars($barang['stok']) ?></div>
                            <div class="card-price">Rp. <?= number_format($barang['harga'], 0, ',', '.') ?></div>
                        </button>
                    </div>
                </div>
            </form>
            <?php endforeach; ?>
        </div>
    </main>

    <div class="bottom-bar">
        <div class="bottom-info">
            <span>Total Amount : Rp. <?= number_format($total_keranjang, 0, ',', '.') ?></span>
            <span>Total Barang : <?= $jumlah_item ?></span>
        </div>
        <a href="checkout.php" class="btn-lanjutkan">Lanjutkan</a>
    </div>

    <div class="modal-overlay" id="itemModal">
        <div class="modal-box">
            <button class="modal-close" id="btnCloseModal">&times;</button>
            <h3 style="margin-bottom:20px; color:var(--sidebar-bg);" id="modalTitle">Tambah Barang Baru</h3>

            <form id="modalForm" action="../process/proses_admin_home.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="aksi_barang" value="1">
                <input type="hidden" name="id_barang" id="form_id_barang" value="">

                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" id="form_nama_barang" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="id_kategori" id="form_id_kategori" class="form-control" required>
                        <?php foreach($kategori_list as $kat): ?>
                            <option value="<?= htmlspecialchars($kat['id_kategori']) ?>">
                                <?= htmlspecialchars($kat['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Stok</label>
                    <input type="text" inputmode="numeric" name="stok" id="form_stok" class="form-control" required>
                    <span id="warning_stok" class="warning-text"></span>
                </div>

                <div class="form-group">
                    <label>Harga Satuan (Rp)</label>
                    <input type="text" inputmode="numeric" name="harga" id="form_harga" class="form-control" required>
                    <span id="warning_harga" class="warning-text"></span>
                </div>

                <div class="form-group">
                    <label>Gambar Produk <span id="pict_hint" style="font-weight:400; color:#888;"></span></label>
                    <input type="file" name="pict" id="form_pict" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn-submit">Simpan</button>
            </form>
        </div>
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
        const modal      = document.getElementById('itemModal');
        const modalTitle = document.getElementById('modalTitle');
        const btnOpenAdd = document.getElementById('btnOpenAdd');
        const btnClose   = document.getElementById('btnCloseModal');
        const modalForm  = document.getElementById('modalForm');

        // Elemen Input & Warning
        const formStok     = document.getElementById('form_stok');
        const formHarga    = document.getElementById('form_harga');
        const warningStok  = document.getElementById('warning_stok');
        const warningHarga = document.getElementById('warning_harga');

        /* ── Fungsi Validasi Super Spesifik ── */
        function validateNumberInput(inputEl, warningEl) {
            const val = inputEl.value;
            let errorMessage = "";

            if (val === "") {
                // Diabaikan karena tag 'required' akan menanganinya
            } 
            else if (val.includes('-')) {
                errorMessage = "Peringatan: Nilai tidak bisa mines (negatif).";
            } 
            // Cek apakah ada karakter selain angka (termasuk operator +, *, /, spasi atau huruf)
            else if (/[^0-9]/.test(val)) {
                errorMessage = "Peringatan: Tidak boleh mengandung tanda operasi, huruf, atau spasi.";
            } 
            else {
                const numericVal = parseInt(val, 10);
                if (numericVal > 2147483647) {
                    errorMessage = "Peringatan: Nilai melebihi batas maksimal (2.147.483.647).";
                }
            }

            // Terapkan UI Warning
            if (errorMessage) {
                warningEl.textContent = errorMessage;
                warningEl.style.display = 'block';
                inputEl.classList.add('input-error');
                return false; // Invalid
            } else {
                warningEl.style.display = 'none';
                inputEl.classList.remove('input-error');
                return true;  // Valid
            }
        }

        // Jalankan validasi secara real-time saat user mengetik
        formStok.addEventListener('input', () => validateNumberInput(formStok, warningStok));
        formHarga.addEventListener('input', () => validateNumberInput(formHarga, warningHarga));

        // Cegah Form Submit jika ada error
        modalForm.addEventListener('submit', function(e) {
            const isStokValid = validateNumberInput(formStok, warningStok);
            const isHargaValid = validateNumberInput(formHarga, warningHarga);

            if (!isStokValid || !isHargaValid) {
                e.preventDefault(); // Batalkan pengiriman ke backend
            }
        });

        /* ── Buka Modal: Mode Tambah ── */
        btnOpenAdd.addEventListener('click', function () {
            resetForm();
            modalTitle.textContent = 'Tambah Barang Baru';
            document.getElementById('pict_hint').textContent = '';
            modal.classList.add('active');
        });
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
        /* ── Buka Modal: Mode Edit ── */
        function openEditModal(event, id) {
            event.stopPropagation(); 
            resetForm();
            modalTitle.textContent = 'Edit Barang (memuat data...)';
            modal.classList.add('active');

            fetch('../process/proses_admin_home.php?get_barang=' + id)
                .then(res => res.json())
                .then(data => {
                    if (!data || !data.id_barang) {
                        alert('Gagal memuat data barang.');
                        modal.classList.remove('active');
                        return;
                    }
                    document.getElementById('form_id_barang').value   = data.id_barang;
                    document.getElementById('form_nama_barang').value = data.nama_barang;
                    document.getElementById('form_id_kategori').value = data.id_kategori;
                    document.getElementById('form_stok').value        = data.stok;
                    document.getElementById('form_harga').value       = data.harga;
                    document.getElementById('pict_hint').textContent   = '(kosongkan jika tidak ingin mengganti gambar)';
                    modalTitle.textContent = 'Edit Barang';

                    // Validasi awal ketika data terisi dari backend
                    validateNumberInput(formStok, warningStok);
                    validateNumberInput(formHarga, warningHarga);
                })
                .catch(() => {
                    alert('Terjadi kesalahan saat mengambil data.');
                    modal.classList.remove('active');
                });
        }

        /* ── Tutup Modal ── */
        btnClose.addEventListener('click', function (e) {
            e.preventDefault();
            modal.classList.remove('active');
        });

        window.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });

        /* ── Reset Form ── */
        function resetForm() {
            modalForm.reset();
            document.getElementById('form_id_barang').value = '';
            
            // Reset status error
            warningStok.style.display = 'none';
            warningHarga.style.display = 'none';
            formStok.classList.remove('input-error');
            formHarga.classList.remove('input-error');
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