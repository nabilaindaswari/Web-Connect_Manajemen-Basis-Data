<?php
session_start();
require_once '../config/database.php';

/* ======================================================
   GUARD: CEK AUTENTIKASI & HAK AKSES KASIR (1-10)
====================================================== */

if (
    !isset($_SESSION['authenticated']) ||
    $_SESSION['authenticated'] !== true ||
    !isset($_SESSION['access_level']) ||
    $_SESSION['access_level'] < 1 ||
    $_SESSION['access_level'] > 10
) {
    header('Location: ../public/login.php?error=accesskasir');
    exit;
}


/* ======================================================
   CEK TIMEZONE & SAPAAN BERDASARKAN WAKTU
====================================================== */


// Set zona waktu ke WIB (Waktu Indonesia Barat)
date_default_timezone_set('Asia/Jakarta');
$jam = (int)date('H'); // Ambil jam saat ini dalam format 24 jam (0-23)
            
// Logika penentuan sapaan
if ($jam >= 4 && $jam < 11) {
    $sapaan = "Selamat Pagi!";
} elseif ($jam >= 11 && $jam < 15) {
    $sapaan = "Selamat Siang!";
} elseif ($jam >= 15 && $jam < 18) {
    $sapaan = "Selamat Sore!";
} else {
    $sapaan = "Selamat Malam!";
}


/* ======================================================
   SESSION KERANJANG & PEMBUATAN TOKEN CHECKOUT
====================================================== */

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// [TAMBAHAN BARU]: Buat token checkout jika belum ada untuk mencegah double submit
if (empty($_SESSION['token_checkout'])) {
    $_SESSION['token_checkout'] = bin2hex(random_bytes(16));
}

/* ======================================================
   AMBIL DATA BARANG + KATEGORI
====================================================== */

/*
|--------------------------------------------------------------------------
| FILTER SORT
|--------------------------------------------------------------------------
*/
$sort = $_GET['sort'] ?? '';
$orderBy = '';

if ($sort === 'termurah') {
    $orderBy = 'ORDER BY barang.harga ASC';
} elseif ($sort === 'termahal') {
    $orderBy = 'ORDER BY barang.harga DESC';
}

/*
|--------------------------------------------------------------------------
| FILTER KATEGORI
|--------------------------------------------------------------------------
*/
$kategoriFilter = $_GET['kategori'] ?? '';
$where = '';
$params = [];

if ($kategoriFilter !== '') {
    $where = 'WHERE barang.id_kategori = ?';
    $params[] = $kategoriFilter;
}

/*
|--------------------------------------------------------------------------
| QUERY FINAL
|--------------------------------------------------------------------------
*/
$query = "
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
    $where
    $orderBy
";

$stmtBarangList = $pdo->prepare($query);

// Eksekusi dengan parameter (Bug execute() ganda sudah dihapus)
$stmtBarangList->execute($params); 
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