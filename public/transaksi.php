<?php
session_start();

/* ======================================================
   [AREA BACKEND] 
   Backend dev akan menaruh logika query INSERT ke tabel 
   transaksi, detail_transaksi, dan update stok di sini.
   Setelah berhasil, session keranjang biasanya dikosongkan.
====================================================== */

// Simulasi data untuk kebutuhan Front-End (Preview Struk)
$tanggal = date('d/m/Y H:i');
$no_struk = 'TRX-' . $nomor_struk;

$total_harga = isset($_POST['total_harga']) ? (int)$_POST['total_harga'] : 0;
$total_bayar = isset($_POST['total_bayar']) ? (int)$_POST['total_bayar'] : 0;
$kembalian = $total_bayar - $total_harga;

// Mengambil data keranjang sebelum dikosongkan oleh backend
$cart_list = $_SESSION['keranjang'] ?? [];

// Dummy fallback jika langsung buka file ini tanpa lewat checkout
// if (empty($cart_list)) {
//     $cart_list = [
//         ['nama_barang' => 'Beras Pandan Wangi 5kg', 'jumlah_barang' => 1, 'harga' => 75000, 'subtotal' => 75000],
//         ['nama_barang' => 'Minyak Goreng Bimoli 2L', 'jumlah_barang' => 2, 'harga' => 35000, 'subtotal' => 70000]
//     ];
//     $total_harga = 145000;
//     $total_bayar = 150000;
//     $kembalian = 5000;
// }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Berhasil - Toko Sembako Indojaya</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --font-main: 'Inter', sans-serif;
            --font-receipt: 'JetBrains Mono', monospace; /* Font ala struk kasir */
            --bg-body: #E5D3B3; /* Mengikuti tema awal admin */
            --bg-card: #ffffff;
            --text-dark: #333333;
            --text-gray: #666666;
            --success-color: #688047;
            --btn-bg: #432E22;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-body);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .success-container {
            width: 100%;
            max-width: 480px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
        }

        /* Notifikasi Sukses */
        .success-header {
            text-align: center;
            animation: fadeInDown 0.5s ease;
        }

        .check-circle {
            width: 64px;
            height: 64px;
            background-color: var(--success-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 4px 12px rgba(104, 128, 71, 0.3);
        }

        .success-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .success-header p {
            color: var(--text-gray);
            font-size: 15px;
        }

        /* Desain Kertas Struk */
        .receipt-card {
            background-color: var(--bg-card);
            width: 100%;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            font-family: var(--font-receipt);
            font-size: 13px;
            position: relative;
            animation: fadeInUp 0.6s ease;
        }

        /* Efek gerigi di bagian atas dan bawah struk */
        .receipt-card::before, .receipt-card::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            height: 10px;
            background-size: 20px 20px;
        }

        .receipt-card::before {
            top: -5px;
            background-image: radial-gradient(circle at 10px 0, transparent 10px, var(--bg-card) 11px);
        }

        .receipt-card::after {
            bottom: -5px;
            background-image: radial-gradient(circle at 10px 20px, transparent 10px, var(--bg-card) 11px);
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 15px;
        }

        .receipt-header h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 4px;
            font-family: var(--font-main);
        }

        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: var(--text-gray);
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .receipt-table th, .receipt-table td {
            padding: 6px 0;
            vertical-align: top;
        }

        .receipt-table th {
            text-align: left;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 8px;
            font-weight: 500;
        }

        .col-qty { width: 15%; text-align: left; }
        .col-item { width: 50%; }
        .col-total { width: 35%; text-align: right; }

        .receipt-summary {
            border-top: 1px dashed #ccc;
            padding-top: 15px;
            margin-top: 5px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .summary-row.grand-total {
            font-weight: 600;
            font-size: 15px;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 25px;
            color: var(--text-gray);
            font-size: 12px;
        }

        /* Tombol Aksi */
        .action-buttons {
            display: flex;
            gap: 12px;
            width: 100%;
            animation: fadeInUp 0.7s ease;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            font-family: var(--font-main);
            font-size: 15px;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary {
            background-color: var(--btn-bg);
            color: white;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .btn-secondary {
            background-color: transparent;
            color: var(--btn-bg);
            border: 1px solid var(--btn-bg);
        }

        .btn-secondary:hover {
            background-color: rgba(67, 46, 34, 0.05);
        }

        /* Keyframes untuk animasi halus */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Mode Print - Menyembunyikan elemen selain struk saat di-print */
        @media print {
            body { background: white; padding: 0; }
            .success-header, .action-buttons { display: none; }
            .receipt-card { box-shadow: none; padding: 0; }
            .receipt-card::before, .receipt-card::after { display: none; }
        }
    </style>
</head>
<body>

    <div class="success-container">
        
        <div class="success-header">
            <div class="check-circle">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h1>Pembayaran Berhasil</h1>
            <p>Terima kasih, transaksi telah tersimpan ke sistem.</p>
        </div>

        <div class="receipt-card">
            <div class="receipt-header">
                <h2>TOKO SEMBAKO INDOJAYA</h2>
                <p>Jl. Contoh Alamat No. 123, Pontianak</p>
            </div>

            <div class="receipt-info">
                <div>
                    <div>No: <?= $no_struk ?></div>
                    <div>Kasir: <?= $kasir ?></div>
                </div>
                <div style="text-align: right;">
                    <div><?= $tanggal ?></div>
                </div>
            </div>

            <table class="receipt-table">
                <thead>
                    <tr>
                        <th class="col-qty">Qty</th>
                        <th class="col-item">Item</th>
                        <th class="col-total">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($cart_list as $item): ?>
                    <tr>
                        <td class="col-qty"><?= $item['jumlah_barang'] ?>x</td>
                        <td class="col-item">
                            <?= htmlspecialchars($item['nama_barang']) ?><br>
                            <span style="color: #888; font-size: 11px;">@ <?= number_format($item['harga'], 0, ',', '.') ?></span>
                        </td>
                        <td class="col-total"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="receipt-summary">
                <div class="summary-row grand-total">
                    <span>TOTAL</span>
                    <span>Rp <?= number_format($total_harga, 0, ',', '.') ?></span>
                </div>
                <div class="summary-row">
                    <span>BAYAR
                    </span>
                    <span>Rp <?= number_format($total_bayar, 0, ',', '.') ?></span>
                </div>
                <div class="summary-row">
                    <span>KEMBALI</span>
                    <span>Rp <?= number_format($kembalian, 0, ',', '.') ?></span>
                </div>
            </div>

            <div class="receipt-footer">
                <p>Terima Kasih Atas Kunjungan Anda</p>
                <p>Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan</p>
            </div>
        </div>

        <div class="action-buttons">
            <button onclick="window.print()" class="btn btn-secondary">
                Cetak Struk
            </button>
            <a href="<?= (isset($_SESSION['access_level']) && $_SESSION['access_level'] >= 11) ? '../public/admin_home.php' : '../public/kasir_home.php' ?>" class="btn btn-primary">← Kembali ke Home</a>
           
        </div>

    </div>

</body>
</html>