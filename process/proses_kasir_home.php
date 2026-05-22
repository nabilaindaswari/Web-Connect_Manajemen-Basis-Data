<?php

require_once '../config/database.php';

/* ======================================================
   SESSION KERANJANG
====================================================== */

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

/* ======================================================
   AMBIL DATA BARANG + KATEGORI
====================================================== */

$sort = $_GET['sort'] ?? '';

$orderBy = '';

if ($sort === 'termurah') {

    $orderBy = 'ORDER BY barang.harga ASC';

} elseif ($sort === 'termahal') {

    $orderBy = 'ORDER BY barang.harga DESC';
}

$kategoriFilter = $_GET['kategori'] ?? '';

if ($kategoriFilter !== '') {

    $orderBy .= "WHERE barang.id_kategori = $kategoriFilter";
}

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
        ON barang.id_kategori = kategori.id_kategori $orderBy ;
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