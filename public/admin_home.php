<?php
session_start();
// ;connect -> memulai session untuk menyimpan data keranjang/login
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- ## bagian konfigurasi meta dan font -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Toko Sembako Indojaya</title>
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

        .sidebar {
            width: 250px;
            background-color: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            color: var(--text-light);
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
            padding-bottom: 120px;
            overflow-y: auto;
            position: relative;
        }

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
        }

        .scroll-track {
            position: absolute;
            right: 15px;
            top: 100px;
            bottom: 40px;
            width: 4px;
            background-color: #432E22;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
            padding-right: 20px;
        }

        .product-card {
            background-color: transparent;
            border-radius: var(--border-radius-card);
            display: flex;
            flex-direction: column;
            height: 280px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            position: relative;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
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

        .img-icon {
            width: 30px;
            height: 30px;
            border: 2px solid #333;
            border-radius: 4px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
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

        .card-bottom {
            background-color: var(--card-bottom-bg);
            flex: 1;
            padding: 15px;
            color: var(--text-light);
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            border-bottom-left-radius: var(--border-radius-card);
            border-bottom-right-radius: var(--border-radius-card);
        }

        .btn-edit-card {
            align-self: flex-start;
            margin-bottom: 8px;
            background-color: var(--btn-edit-bg);
            color: white;
            border: none;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 11px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
            position: relative;
            z-index: 10;
        }

        .card-kategori {
            font-size: 11px;
            margin-bottom: 2px;
            margin-top: 5px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .card-stok {
            font-size: 11px;
            margin-bottom: 5px;
        }

        .card-price {
            font-size: 16px;
            font-weight: 500;
        }

        .bottom-bar {
            position: fixed;
            bottom: 0;
            left: 250px; 
            width: calc(100% - 250px);
            background-color: #989267;
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
            background-color: #3F2921;
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

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
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
            top: 15px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            border: none;
            background: none;
            color: var(--sidebar-bg);
        }
        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }
        .form-control {
            padding: 10px;
            border: 1px solid var(--top-btn-bg);
            border-radius: 8px;
            background-color: #EFE4D3;
        }
        
        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
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

    <!-- ## bagian utama konten dasboard admin -->
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

            <!-- ;tombol add -> btnOpenAdd (memicu modal form) -->
                <button class="btn-tambah" id="btnOpenAdd">
                    <!-- Plus Icon -->
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    Tambahkan Produk Baru
                </button>
            </div>
        </div>

        <div class="scroll-track"></div>

        <!-- ## bagian grid daftar produk -->
        <div class="product-grid">
            
        <!-- ;connect -> $barang_list, query nya join antara kategori dan barang -->
            <?php foreach($barang_list as $index => $barang): ?>
            <div class="product-card">
                
                <!-- ;connect -> logic submit form langsung mengarah ke proses add_cart.php -->
                <form action="../process/add_cart.php" method="POST" style="height: 100%; display: flex; flex-direction: column; cursor: pointer; margin: 0;" onclick="this.submit()">
                    
                    <!-- ## bagian ini untuk perhitungan transaksi-->
                            <!-- ;connect -> ID Barang agar sistem tahu barang mana yang dibeli -->
                            <input type="hidden" name="id_barang" value="<?= htmlspecialchars($barang['id_barang']) ?>">

                            <!-- ;connect -> ambil Harga Barang dari database untuk dihitung total transaksinya -->
                            <input type="hidden" name="harga" value="<?= htmlspecialchars($barang['harga']) ?>">

                            <!-- ;connect -> MENETAPKAN JUMLAH PEMBELIAN DEFAULT -->
                            <input type="hidden" name="jumlah_barang" value="1">

                    <!-- ## ini untuk tampilan tiap card-->
                            <!-- ini untuk bagian atas card, atau bagian gambarnya -->
                            
                            <<div class="card-top">
                                <!-- ;connect -> back end tolong dong di cek jumlah barang ini di dalam session keranjang, masukin dalam qty_dipesan -->
                                <!-- ;connect -> logic penanda badge nomor dinamis, hanya muncul jika qty > 0 -->
                                <?php if($qty_dipesan > 0): ?>
                                    <div class="card-badge"><?= $qty_dipesan ?></div>
                                <?php endif; ?>
                                
                                <!-- ;connect -> ambil gambar dari database barang.pict yang terintegrasi path nya dengan /public/menuPict/ -->
                                <img src="../public/menuPict/<?= htmlspecialchars($barang['pict']) ?>" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                            
                            <!-- ## bagian bawah info di tiap card -->
                            <div class="card-bottom">
                    <!-- ## tombol edit di card tiap barang-->
                    <!-- ;tombol edit -> btn-edit-card memicu modal terbuka dengan membawa parameter id_barang -->
                            <button type="button" class="btn-edit-card" onclick="openEditModal(event, <?= $barang['id_barang'] ?>)">edit</button>
                                
                                <!-- ;connect -> ambil id_kategori, ditambahkan padding 0 di depan -->
                                <div class="card-kategori">Kategori : <?= str_pad($barang['id_kategori'], 2, '0', STR_PAD_LEFT) ?></div>
                                
                                <!-- ;connect -> ambil nama_barang dari barang.nama_barang-->
                                <div class="card-title"><?= htmlspecialchars($barang['nama_barang']) ?></div>

                                <!-- ;connect -> ambil stok dari barang.stok-->
                                <div class="card-stok">Stok : <?= htmlspecialchars($barang['stok']) ?></div>

                                <!-- ;connect -> ambil harga dari barang.harga lalu diformat ke rupiah -->
                                <div class="card-price">Rp. <?= number_format($barang['harga'], 0, ',', '.') ?></div>
                            </div>
                </form>
            </div>
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

    <!--## form edit atau tambah (Modal Box Pop up) -->
    <div class="modal-overlay" id="itemModal">
        <div class="modal-box">
            <!-- ;tombol -> close/tutup modal -->
            <button class="modal-close" id="btnCloseModal">&times;</button>
            <h3 style="margin-bottom: 20px; color: var(--sidebar-bg);" id="modalTitle">Tambah Barang Baru</h3>
            
            <!-- ;connect -> action form diarahkan ke proses_barang.php untuk ditangkap dan masuk ke database -->
            <form action="../process/proses_barang.php" method="POST" enctype="multipart/form-data">
                
                <!-- ;input -> hidden untuk menyimpan id barang saat mode edit berjalan -->
                <input type="hidden" name="id_barang" id="form_id_barang" value="">
                
                <div class="form-group">
                    <label>Nama Barang</label>
                    <!-- ;input -> data nama barang -->
                    <input type="text" name="nama_barang" id="form_nama_barang" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <!-- ;input -> select data kategori -->
                    <select name="id_kategori" id="form_id_kategori" class="form-control" required>
                        <!-- ;connect -> looping opsi list kategori dari database -->
                        <?php foreach($kategori_list as $kat): ?>
                            <option value="<?= htmlspecialchars($kat['id_kategori']) ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Stok Awal</label>
                    <!-- ;input -> data stok berbentuk angka -->
                    <input type="number" name="stok" id="form_stok" class="form-control" required min="0">
                </div>

                <div class="form-group">
                    <label>Harga Satuan (Rp)</label>
                    <!-- ;input -> data harga barang berbentuk angka -->
                    <input type="number" name="harga" id="form_harga" class="form-control" required min="0">
                </div>

                <div class="form-group">
                    <label>Gambar Produk</label>
                    <!-- ;input -> unggah file foto untuk masuk ke dalam direktori -->
                    <input type="file" name="pict" id="form_pict" class="form-control" accept="image/*">
                </div>

                <!-- ;tombol submit -> btn-submit (simpan ke database) -->
                <button type="submit" class="btn-submit">Simpan</button>
            </form>
        </div>
    </div>

    <!-- ## script js untuk logika dan interaksi modal (tambah/edit barang), nanti boleh di hapus dan dipindah ke backend -->
    <script>
        const modal = document.getElementById('itemModal');
        const modalTitle = document.getElementById('modalTitle');
        const btnOpenAdd = document.getElementById('btnOpenAdd');
        const btnClose = document.getElementById('btnCloseModal');

        // ;logic -> reset isi form jika tombol 'tambah barang baru' diklik
        if(btnOpenAdd) {
            btnOpenAdd.addEventListener('click', function() {
                document.getElementById('form_id_barang').value = '';
                modalTitle.textContent = "Tambah Barang Baru";
                modal.classList.add('active');
            });
        }

        // ;logic -> openEditModal untuk membuka modal khusus mode edit data barang
        function openEditModal(event, id) {
            event.stopPropagation(); // Mencegah form parent di card tersubmit secara tidak sengaja
            document.getElementById('form_id_barang').value = id;
            modalTitle.textContent = "Edit Barang";
            // Logic to fetch details should go here
            modal.classList.add('active');
        }

        // ;logic -> untuk menutup modal box 
        btnClose.addEventListener('click', function(e) {
            e.preventDefault();
            modal.classList.remove('active');
        });
        
        // ;logic -> untuk menutup modal ketika user klik background di luar box
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    </script>
</body>
</html>