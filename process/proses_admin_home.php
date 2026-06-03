<?php
session_start();
require_once '../config/database.php';

/* ======================================================
   GUARD: CEK AUTENTIKASI & HAK AKSES ADMIN (11-20)
====================================================== */

if (
    !isset($_SESSION['authenticated']) ||
    $_SESSION['authenticated'] !== true ||
    !isset($_SESSION['access_level']) ||
    $_SESSION['access_level'] < 11 ||
    $_SESSION['access_level'] > 20
) {
    header('Location: ../public/login.php?error=accessadmin');
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
   HANDLE AKSI POST (TAMBAH / EDIT BARANG)
   Dipicu oleh form modal di admin_home.php
====================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_barang'])) {

    $id_barang   = isset($_POST['id_barang']) && $_POST['id_barang'] !== '' ? (int)$_POST['id_barang'] : null;
    $nama_barang = trim($_POST['nama_barang'] ?? '');
    $id_kategori = (int)($_POST['id_kategori'] ?? 0);
    $stok        = (int)($_POST['stok'] ?? 0);
    $harga       = (int)($_POST['harga'] ?? 0);

    /* --- Handle Upload Gambar --- */
    $nama_file = null;

    if (isset($_FILES['pict']) && $_FILES['pict']['error'] === UPLOAD_ERR_OK) {
        $ext_allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext         = strtolower(pathinfo($_FILES['pict']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $ext_allowed)) {
            $nama_file = uniqid('pict_', true) . '.' . $ext;
            $tujuan    = __DIR__ . '/../public/menuPict/' . $nama_file;

            if (!move_uploaded_file($_FILES['pict']['tmp_name'], $tujuan)) {
                $nama_file = null; // Gagal upload, abaikan
            }
        }
    }

    if ($id_barang === null) {
        /* ------------------------------------------------
           MODE TAMBAH BARANG BARU
        ------------------------------------------------ */
        $pict_value = $nama_file ?? 'default.png';

        $stmtInsert = $pdo->prepare("
            INSERT INTO barang (nama_barang, id_kategori, stok, harga, pict)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmtInsert->execute([$nama_barang, $id_kategori, $stok, $harga, $pict_value]);

    } else {
        /* ------------------------------------------------
           MODE EDIT BARANG
        ------------------------------------------------ */
        if ($nama_file !== null) {
            /* Update termasuk gambar baru */
            $stmtUpdate = $pdo->prepare("
                UPDATE barang
                SET nama_barang = ?,
                    id_kategori = ?,
                    stok        = ?,
                    harga       = ?,
                    pict        = ?
                WHERE id_barang = ?
            ");
            $stmtUpdate->execute([$nama_barang, $id_kategori, $stok, $harga, $nama_file, $id_barang]);
        } else {
            /* Update tanpa mengganti gambar */
            $stmtUpdate = $pdo->prepare("
                UPDATE barang
                SET nama_barang = ?,
                    id_kategori = ?,
                    stok        = ?,
                    harga       = ?
                WHERE id_barang = ?
            ");
            $stmtUpdate->execute([$nama_barang, $id_kategori, $stok, $harga, $id_barang]);
        }
    }

    /* Redirect kembali agar tidak resubmit saat refresh */
    header('Location: ../public/admin_home.php');
    exit;
}

/* ======================================================
   HANDLE AJAX: AMBIL DATA BARANG UNTUK MODAL EDIT
   Dipanggil via fetch() di admin_home.php
====================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_barang'])) {
    $id = (int)$_GET['get_barang'];

    $stmt = $pdo->prepare("
        SELECT id_barang, nama_barang, id_kategori, stok, harga, pict
        FROM barang
        WHERE id_barang = ?
    ");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($data ?: []);
    exit;
}

/* ======================================================
   AMBIL DATA BARANG + KATEGORI
====================================================== */

/*
|--------------------------------------------------------------------------
| FILTER SORT
|--------------------------------------------------------------------------
*/
$sort    = $_GET['sort'] ?? '';
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
$where          = '';
$params         = [];

if ($kategoriFilter !== '') {
    $where    = 'WHERE barang.id_kategori = ?';
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
$barang_list = $stmtBarangList->fetchAll(PDO::FETCH_ASSOC);

/* ======================================================
   AMBIL DATA KATEGORI
====================================================== */

$stmtKategori = $pdo->prepare("SELECT * FROM kategori");
$stmtKategori->execute();
$kategori_list = $stmtKategori->fetchAll(PDO::FETCH_ASSOC);

/* ======================================================
   HITUNG TOTAL KERANJANG
====================================================== */

$total_keranjang = 0;
$jumlah_item     = 0;

foreach ($_SESSION['keranjang'] as $item) {
    $subtotal         = $item['harga'] * $item['jumlah_barang'];
    $total_keranjang += $subtotal;
    $jumlah_item     += $item['jumlah_barang'];
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