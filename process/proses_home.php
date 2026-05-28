<?php

require_once '../config/database.php';

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