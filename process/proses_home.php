<?php

require_once '../config/database.php';

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

$stmtBarangList->execute($params);


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

?>