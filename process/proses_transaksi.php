<?php
session_start();
require_once '../config/database.php';

// Pastikan PDO menggunakan mode Exception agar bisa menangkap SIGNAL dari database
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (empty($_SESSION['keranjang'])) {
    // Set variabel untuk ditampilkan di halaman error
    $judul_error = "Transaksi Sudah Diproses!";
    $pesan_error = "Keranjang Anda kosong atau halaman di-refresh. Silakan kembali ke halaman kasir untuk transaksi baru.";
    $link_kembali = "../public/kasir_home.php";
    
    // Panggil interface error
    require_once '../public/error_transaksi.php';
    die(); // Berhenti di sini, jangan hubungi database!
}

$id_kasir        = $_SESSION['user_id'] ?? null;
$id_metode       = (int)($_POST['id_metode'] ?? 0);
$total_bayar     = (int)($_POST['total_bayar'] ?? 0);
$total_harga     = (int)($_POST['total_harga'] ?? 0);

// 1. Ambil token transaksi dari form POST. 
// Jika tidak ada (fallback), buat token unik baru (meski idealnya dari form frontend)
$token_transaksi = $_POST['token_transaksi'] ?? bin2hex(random_bytes(16));

# Pembuatan JSON Keranjang berasal dari ID Barang dan Jumlah yang dikirim dari form checkout
$p_keranjang     = [];
if (!empty($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $item) {
        $p_keranjang[] = [
            'id_barang' => (int)$item['id_barang'],
            'jumlah'    => (int)$item['jumlah_barang']
        ];
    }
}
$p_json_keranjang = json_encode($p_keranjang);

/* Ambil nama_lengkap untuk ditampilkan di struk */
$stmtUser = $pdo->prepare("SELECT nama_lengkap FROM kasir WHERE id_kasir = ? LIMIT 1");
$stmtUser->execute([$id_kasir]);
$userRow       = $stmtUser->fetch(PDO::FETCH_ASSOC);
$kasir = $userRow ? $userRow['nama_lengkap'] : ($username_session ?? 'Kasir');

// 2. Gunakan blok TRY...CATCH untuk menangani transaksi
try {
    $stmtTransaksi = $pdo->prepare("
        CALL SP_Checkout_Kasir(
            :p_token_transaksi,  -- Tambahan parameter baru
            :p_id_kasir,
            :p_id_metode,
            :p_total_bayar,
            :p_json_keranjang,
            @kembalian,
            @idtransaksix
        )
    ");

    $stmtTransaksi->execute([
        ':p_token_transaksi' => $token_transaksi,
        ':p_id_kasir'        => $id_kasir,
        ':p_id_metode'       => $id_metode,
        ':p_total_bayar'     => $total_bayar,
        ':p_json_keranjang'  => $p_json_keranjang,
    ]);

    $stmtTransaksi->closeCursor();

    // Ambil nilai output (OUT parameters)
    $getData = $pdo->query("
        SELECT @kembalian AS kembalian,
               @idtransaksix AS idtransaksix
    ")->fetch(PDO::FETCH_ASSOC);

    $kembalian = $getData['kembalian'];
    $nomor_struk = $getData['idtransaksix'];

    // [PENTING] Kosongkan keranjang setelah transaksi sukses agar tidak bisa dibeli lagi
    unset($_SESSION['keranjang']);

    // Redirect ke halaman sukses / cetak struk (PRG Pattern)
    // header("Location: struk.php?id=" . $nomor_struk);
    // exit();

} catch (PDOException $e) {
    // 3. Tangkap Error dari Stored Procedure (seperti stok kurang, uang kurang, atau duplikat token)
    
    $pesan_error = $e->getMessage();
    
    // Mengecek apakah error disebabkan oleh token ganda (kode 1062)
    if ($e->getCode() == 1062 || strpos($pesan_error, '1062') !== false) {
        $pesan_error = "Transaksi sudah diproses sebelumnya. Mohon periksa kembali riwayat transaksi Anda.";
    } else {
        // Membersihkan pesan error default dari SQL agar lebih ramah dibaca pengguna
        $pesan_error = preg_replace('/SQLSTATE\[.*\]:.*:/', '', $pesan_error);
    }

    // Set variabel untuk ditampilkan di halaman error
    $judul_error = "Gagal Memproses Transaksi!";
    // $pesan_error sudah terisi dari logika exception di atas
    $link_kembali = "javascript:history.back()"; 
    
    // Panggil interface error
    require_once '../public/error_transaksi.php';
    die();
}
?>