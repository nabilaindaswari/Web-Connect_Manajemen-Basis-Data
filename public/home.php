<?php
require_once '../config/database.php';

try {
    $data = $conn->query("SELECT * FROM barang")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $data = [];
}

try {
    $s = $conn->prepare("SELECT COALESCE(SUM(dt.jumlah_barang),0) FROM detail_transaksi dt JOIN transaksi t ON dt.id_transaksi=t.id_transaksi WHERE t.statuss=1 AND DATE(t.tanggal_transaksi)=CURDATE()");
    $s->execute(); $barangTerjualHari = $s->fetchColumn();
} catch (PDOException $e) { $barangTerjualHari = 0; }

try {
    $s = $conn->prepare("SELECT COALESCE(SUM(total_harga),0) FROM transaksi WHERE statuss=1 AND DATE(tanggal_transaksi)=CURDATE()");
    $s->execute(); $pendapatanHari = $s->fetchColumn();
} catch (PDOException $e) { $pendapatanHari = 0; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<title>Dashboard POS</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial}
body{display:flex;background:#121212;color:#e0e0e0}
.sidebar{width:220px;height:100vh;background:#1e1e1e;padding:20px}
.sidebar h2{margin-bottom:30px}
.sidebar a{display:block;padding:10px;margin-bottom:10px;color:#ccc;text-decoration:none;border-radius:5px}
.sidebar a:hover{background:#333}
.main{flex:1;padding:20px}
.topbar{margin-bottom:20px;font-size:18px}
.cards{display:flex;gap:15px;margin-bottom:20px}
.card{width:200px;padding:15px;border-radius:12px;color:white;position:relative}
.card-1{background:linear-gradient(to left,#1e3c72,#2a5298)}
.card-2{background:linear-gradient(to left,#D4A017,#ffd700)}
.card-3{background:linear-gradient(to left,#ff416c,#ff4b2b)}
.card small{display:block;font-size:11px;opacity:.8;margin-bottom:5px}
.card h2{font-size:20px;margin-top:5px}
.card-icon{position:absolute;right:15px;top:15px;font-size:20px;opacity:.7}
.header-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.header-bar h2{font-size:24px}
.btn-add{background:#f5c542;color:black;padding:10px 15px;border-radius:8px;text-decoration:none;font-weight:bold}
.filter-bar{display:flex;gap:10px;margin-bottom:20px}
.filter-bar input,.filter-bar select{padding:8px;background:#1e1e1e;border:none;color:white;border-radius:6px}
.list-header{display:flex;padding:10px 15px;font-size:12px;color:#aaa;border-bottom:1px solid #333;margin-bottom:10px}
.item{display:flex;align-items:center;padding:12px 15px;background:#1e1e1e;border-radius:10px;margin-bottom:8px;border:1px solid #2a2a2a}
.col{flex:1}
.col.nama{flex:2}
.col.harga{width:120px}
.col.stok{width:80px}
.col.status{width:120px}
.col.action{width:80px;text-align:right}
.item .nama strong{display:block}
.item .nama small{font-size:11px;color:#888}
.status-badge{padding:4px 8px;border-radius:6px;font-size:11px}
.status-yes{background:#2e7d32}
.status-no{background:#c62828}
.action a{margin-left:8px;text-decoration:none;color:white}
</style>
</head>
<body>

<div class="sidebar">
    <h2>Toko Sembako</h2>
    <a href="#">Dashboard</a>
    <a href="#">Barang</a>
    <a href="#">Transaksi</a>
    <a href="#">Laporan</a>
</div>

<div class="main">
    <div class="topbar">Selamat Datang, Admin!</div>

    <div class="cards">
        <div class="card card-1"><div class="card-icon">📦</div><small>TOTAL BARANG</small><h2><?= count($data) ?></h2></div>
        <div class="card card-2"><div class="card-icon">🛒</div><small>BARANG TERJUAL / HARI</small><h2><?= $barangTerjualHari ?></h2></div>
        <div class="card card-3"><div class="card-icon">💰</div><small>HASIL PENJUALAN / HARI</small><h2>Rp <?= number_format($pendapatanHari,0,',','.') ?></h2></div>
    </div>

    <div class="header-bar">
        <h2>📊 Dashboard</h2>
        <a href="tambah.php" class="btn-add">+ Tambah Barang</a>
    </div>

    <div class="filter-bar">
        <input type="text" placeholder="Cari barang...">
        <select><option>Semua Kategori</option></select>
    </div>

    <div class="list-header">
        <div class="col nama">Nama</div>
        <div class="col harga">Harga</div>
        <div class="col stok">Stok</div>
        <div class="col status">Status</div>
        <div class="col action">Action</div>
    </div>

    <?php foreach ($data as $r): ?>
    <div class="item">
        <div class="col nama">
            <strong><?= $r['nama_barang'] ?></strong>
            <small>ID: <?= $r['id_barang'] ?> • Kategori: <?= $r['id_kategori'] ?></small>
        </div>
        <div class="col harga">Rp <?= number_format($r['harga'],0,',','.') ?></div>
        <div class="col stok"><?= $r['stok'] ?></div>
        <div class="col status">
            <span class="status-badge <?= $r['stok']>0 ? 'status-yes' : 'status-no' ?>">
                <?= $r['stok']>0 ? 'Tersedia' : 'Habis' ?>
            </span>
        </div>
        <div class="col action">
            <a href="edit.php?id_barang=<?= $r['id_barang'] ?>">✏️</a>
            <a href="hapus.php?id_barang=<?= $r['id_barang'] ?>">🗑️</a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

</body>
</html>