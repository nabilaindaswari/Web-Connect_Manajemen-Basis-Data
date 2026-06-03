<?php
session_start();
require_once '../config/database.php';

/* ======================================================
   CEK SESSION KERANJANG
====================================================== */

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}


/* ======================================================
   VARIABLE CHECKOUT
====================================================== */

$cart_list = [];
$total_harga = 0;


/* ======================================================
   AMBIL DATA KERANJANG
====================================================== */

foreach ($_SESSION['keranjang'] as $item) {

    $subtotal = $item['harga'] * $item['jumlah_barang'];

    $cart_list[] = [
        'id_barang'     => $item['id_barang'],
        'nama_barang'   => $item['nama_barang'],
        'harga'         => $item['harga'],
        'jumlah_barang' => $item['jumlah_barang'],
        'subtotal'      => $subtotal
    ];

    $total_harga += $subtotal;
}


/* ======================================================
   AMBIL METODE PEMBAYARAN
====================================================== */

$stmtMetode = $pdo->prepare("
    SELECT *
    FROM metode_pembayaran
");

$stmtMetode->execute();

$metode_list = $stmtMetode->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Toko Sembako Indojaya</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-main: 'Google Sans', 'Inter', sans-serif;
            --bg-body: #fbfbfd;
            --bg-card: #ffffff;
            --color-sidebar-dark: #4e342e;
            --color-sidebar-light: #eaddd2;
            --color-bottom-bar: #8d7b68;
            --color-text-dark: #333333;
            --shadow-soft: 0 4px 12px rgba(0, 0, 0, 0.04);
            --border-radius: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-body);
            color: var(--color-text-dark);
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Main Content Styling */
        .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
            padding-bottom: 100px;
            display: flex;
            justify-content: center;
        }

        .table-container {
            width: 100%;
            max-width: 900px;
            background: var(--bg-card);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            padding: 30px;
            align-self: flex-start;
        }

        .header-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            font-weight: 600;
            color: var(--color-sidebar-dark);
            background-color: rgba(234, 221, 210, 0.3);
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Bottom Bar Styling */
        .bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: var(--color-sidebar-light);
            padding: 15px 40px;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.05);
            z-index: 10;
        }

        /* =========================================
           FULL-WIDTH BOTTOM BAR BUTTON
        ========================================= */
        .bottom-bar-btn {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #688047;
            color: #ffffff;
            border: none;
            padding: 22px 20px;
            font-size: 20px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: center; /* Menengahkan teks di dalam bar */
            align-items: center;
            gap: 15px; /* Jarak antara teks harga dan teks metode */
            transition: background-color 0.2s ease-in-out;
            font-family: var(--font-main);
            z-index: 100;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.1);
        }

        .bottom-bar-btn:hover:not(:disabled) {
            background-color: #556b39;
        }

        .bottom-bar-btn:disabled {
            background-color: #cccccc;
            color: #888888;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Modal Styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-box {
            background-color: var(--bg-card);
            border-radius: var(--border-radius);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            position: relative;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .modal-box {
            transform: translateY(0);
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            color: #888;
            border: none;
            background: none;
        }

        .modal-close:hover {
            color: #333;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
            color: var(--color-sidebar-dark);
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 14px;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        .form-control {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-family: var(--font-main);
            font-size: 14px;
            background-color: #fafafa;
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

        .form-control:focus {
            outline: none;
            border-color: var(--color-sidebar-dark);
            background-color: #fff;
        }

        .readonly-text {
            font-size: 16px;
            font-weight: 600;
            padding: 10px 0;
            color: var(--color-sidebar-dark);
        }

        .btn-submit {
            width: 100%;
            background-color: var(--color-sidebar-dark);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            font-family: var(--font-main);
            transition: background 0.3s;
        }

        .btn-submit:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
        .btn-submit:not(:disabled):hover {
            background-color: #3e2a24;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: var(--color-sidebar-dark);
            font-weight: 500;
        }
        .btn-back:hover { text-decoration: underline; }

    </style>
</head>
<body>

    <main class="main-content">
        <div class="table-container">
            

            <a href="<?= (isset($_SESSION['access_level']) && $_SESSION['access_level'] >= 11) ? '../public/admin_home.php' : '../public/kasir_home.php' ?>" class="btn-back">← Kembali</a>
           
            <h2 class="header-title">Rincian Pesanan</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th class="text-right">Harga</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php if(empty($cart_list)): ?>
                        <tr><td colspan="5" class="text-center">Keranjang kosong</td></tr>
                    <?php else: ?>
                        <?php $no=1; foreach($cart_list as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                            <td class="text-right">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                            <td class="text-center"><?= htmlspecialchars($item['jumlah_barang']) ?></td>
                            <td class="text-right">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Bottom Bar -->
    <div class="bottom-bar">
        <button type="button" class="bottom-bar-btn" id="btnOpenModal" <?= empty($cart_list) ? 'disabled' : '' ?>>
        Total Harga: Rp <?= number_format($total_harga, 0, ',', '.') ?>
        </button>
    </div>

    <!-- Modal Pembayaran -->
    <div class="modal-overlay" id="paymentModal">
        <div class="modal-box">
            <button class="modal-close" id="btnCloseModal">&times;</button>
            <h3 class="modal-title">Pembayaran</h3>
            
            <form action="../public/transaksi.php" method="POST">
                
                <input type="hidden" name="total_harga" id="input_total_harga" value="<?= $total_harga ?>">
                
                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <select name="id_metode" class="form-control" required>
                        
                        <?php foreach($metode_list as $metode): ?>
                            <option value="<?= htmlspecialchars($metode['id_metode']) ?>"><?= htmlspecialchars($metode['nama_metode']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Total Harga</label>
                    <div class="readonly-text">Rp <?= number_format($total_harga, 0, ',', '.') ?></div>
                </div>

                <div class="form-group">
                    <label for="total_bayar">Uang Diterima (Rp)</label>
                    <input type="number" name="total_bayar" id="total_bayar" class="form-control" placeholder="Masukkan uang diterima" required min="0">
                </div>

                <div class="form-group">
                    <label>Kembalian</label>
                    <div class="readonly-text" style="color: #688047;">Rp <span id="kembalian">0</span></div>
                </div>

                <button type="submit" class="btn-submit" id="btnSubmitPayment" disabled>Selesai / Bayar</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('paymentModal');
            const btnOpen = document.getElementById('btnOpenModal');
            const btnClose = document.getElementById('btnCloseModal');
            const inputBayar = document.getElementById('total_bayar');
            const spanKembalian = document.getElementById('kembalian');
            const btnSubmit = document.getElementById('btnSubmitPayment');
            
            // Variable dari PHP ke JS
            const totalHarga = parseInt(document.getElementById('input_total_harga').value) || 0;

            // Buka Modal
            if(btnOpen) {
                btnOpen.addEventListener('click', function() {
                    modal.classList.add('active');
                });
            }

            // Tutup Modal
            btnClose.addEventListener('click', function(e) {
                e.preventDefault();
                modal.classList.remove('active');
            });

            // Tutup Modal saat klik overlay luar
            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });

            // Kalkulasi Kembalian
            inputBayar.addEventListener('input', function() {
                const uangDiterima = parseInt(this.value) || 0;
                const kembalian = uangDiterima - totalHarga;

                if (kembalian >= 0) {
                    // Format number to Rupiah string format
                    spanKembalian.textContent = kembalian.toLocaleString('id-ID');
                    btnSubmit.disabled = false;
                } else {
                    spanKembalian.textContent = "0 (Kurang)";
                    btnSubmit.disabled = true;
                }
            });
        });
    </script>
</body>
</html>
