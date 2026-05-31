# Sistem Informasi Point of Sale (POS) Toko Sembako Indojaya

Project Mata Kuliah Manajemen Basis Data
Program Studi Informatika Fakultas Teknik
Universitas Tanjungpura

---

## Anggota Kelompok

1. Nabila Indaswari (D1041241021)
2. Azkafalah Cendikia Suryatmaja (D1041241084)
3. Rizky Hadi (D1041241092)

---

## Deskripsi Project

Sistem Informasi Point of Sale (POS) Toko Sembako Indojaya merupakan aplikasi kasir berbasis web yang dirancang untuk membantu proses transaksi penjualan pada toko sembako. Sistem ini dibuat untuk menggantikan pencatatan manual yang rentan terhadap kesalahan, seperti salah menghitung total belanja, salah mencatat stok, serta kesulitan dalam melihat riwayat transaksi.

Sistem ini menyediakan fitur untuk menampilkan barang, mengelola data barang, mengelola kategori, melakukan transaksi penjualan, menghitung total harga, menghitung kembalian, memperbarui stok secara otomatis, serta menyimpan riwayat transaksi. Pengguna dalam sistem terdiri dari pengguna umum, kasir, dan kasir admin.

Pada sisi database, sistem menerapkan beberapa konsep Manajemen Basis Data seperti relasi antar tabel, primary key, foreign key, view, stored procedure, function, trigger, indexing, transaction control, serta user privilege.

---

## Teknologi yang Digunakan

* PHP Native
* MySQL Database
* PDO MySQL
* HTML
* CSS
* JavaScript
* XAMPP
* Session Authentication
* Role-Based Access Control

---

## Struktur Folder Project

```text
WEB-CONNECT_MANAGEMENT_BASIS_DATA/
│
├── config/
│   └── database.php
│
├── process/
│   ├── add_cart.php
│   ├── hash.php
│   ├── insert.php
│   ├── kurang_cart.php
│   ├── proses_admin_home.php
│   ├── proses_home.php
│   ├── proses_kasir_home.php
│   ├── proses_login.php
│   ├── proses_register.php
│   ├── proses_transaksi.php
│   └── tempCodeRunnerFile.php
│
├── public/
│   ├── asset/
│   ├── menuPict/
│   ├── admin_home.old.php
│   ├── admin_home.php
│   ├── checkout.php
│   ├── home.php
│   ├── kasir_home.php
│   ├── login.php
│   └── transaksi.php
│
├── tokosembako.sql
└── README.md
```

---

## Cara Menjalankan Project di Localhost

### 1. Install dan Jalankan XAMPP

1. Download dan install XAMPP.
2. Buka XAMPP Control Panel.
3. Jalankan module:

   * Apache
   * MySQL
4. Pastikan Apache dan MySQL berstatus Running.

---

### 2. Pindahkan Project ke Folder htdocs

Copy folder project ke dalam direktori `htdocs`.

Contoh lokasi folder:

```text
C:\xampp\htdocs\WEB-CONNECT_MANAGEMENT_BASIS_DATA\
```

Pastikan struktur folder project sudah benar, misalnya:

```text
C:\xampp\htdocs\WEB-CONNECT_MANAGEMENT_BASIS_DATA\config\database.php
C:\xampp\htdocs\WEB-CONNECT_MANAGEMENT_BASIS_DATA\public\home.php
C:\xampp\htdocs\WEB-CONNECT_MANAGEMENT_BASIS_DATA\public\login.php
```

---

### 3. Import Database

1. Buka browser.
2. Akses phpMyAdmin:

```text
http://localhost/phpmyadmin
```

3. Klik menu **Import**.
4. Pilih file database:

```text
tokosembako.sql
```

5. Pastikan format file adalah **SQL**.
6. Klik **Go** atau **Import**.
7. Tunggu hingga proses import selesai.

Jika berhasil, database `tokosembako` akan muncul beserta tabel dan data yang digunakan oleh sistem.

---

### 4. Atur Koneksi Database

Buka file:

```text
config/database.php
```

Sesuaikan konfigurasi koneksi database:

```php
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "tokosembako";

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Koneksi Gagal: " . $e->getMessage();
}
?>
```

Jika MySQL Anda menggunakan password, isi bagian $pass sesuai password MySQL yang digunakan.

---

### 5. Jalankan Aplikasi

Buka browser dan akses:

```text
http://localhost/WEB-CONNECT_MANAGEMENT_BASIS_DATA/public/home.php
```

Untuk login, akses:

```text
http://localhost/WEB-CONNECT_MANAGEMENT_BASIS_DATA/public/login.php
```

---

## Fitur Sistem

### 1. Halaman Pengguna Umum

* Melihat daftar barang
* Melihat detail barang
* Melihat barang berdasarkan kategori
* Melakukan pengurutan barang berdasarkan harga

---

### 2. Autentikasi Pengguna

* Login kasir
* Login kasir admin
* Logout
* Session authentication
* Proteksi halaman berdasarkan level akses

---

### 3. Manajemen Barang

* Menampilkan daftar barang
* Menambahkan barang
* Mengubah data barang
* Mengelola harga barang
* Mengelola stok barang
* Menampilkan barang berdasarkan kategori

---

### 4. Manajemen Keranjang

* Menambahkan barang ke keranjang
* Mengurangi jumlah barang dalam keranjang
* Menyimpan data keranjang menggunakan session
* Menghitung subtotal setiap item

---

### 5. Transaksi Penjualan

* Memproses transaksi penjualan
* Menghitung total harga
* Menghitung total bayar
* Menghitung kembalian
* Menyimpan data transaksi
* Menyimpan detail transaksi
* Mengurangi stok barang secara otomatis
* Menampilkan halaman checkout

---

### 6. Role-Based Access Control

Sistem menggunakan kolom `access_level` pada tabel `kasir` untuk membedakan hak akses pengguna.

| Access Level | Role        | Hak Akses                                              |
| ------------ | ----------- | ------------------------------------------------------ |
| 1 - 10       | Kasir       | Melakukan transaksi dan checkout                       |
| 11 - 20      | Kasir Admin | Mengelola barang, kategori, stok, harga, dan transaksi |

---

## Struktur Database

Database `tokosembako` terdiri dari beberapa tabel utama:

* `barang`
* `kategori`
* `kasir`
* `transaksi`
* `detail_transaksi`
* `metode_pembayaran`

Relasi utama dalam database:

* Satu kategori dapat memiliki banyak barang.
* Satu kasir dapat melayani banyak transaksi.
* Satu metode pembayaran dapat digunakan pada banyak transaksi.
* Satu transaksi dapat memiliki banyak detail transaksi.
* Satu barang dapat muncul pada banyak detail transaksi.

---

## Konsep Database yang Diterapkan

Project ini menerapkan beberapa konsep Manajemen Basis Data, yaitu:

* Entity Relationship Diagram (ERD)
* Skema relasional
* Primary Key
* Foreign Key
* Entity Integrity
* Referential Integrity
* Domain Integrity
* View
* Stored Procedure
* Function
* Trigger
* Indexing
* Transaction Control Language
* Commit dan Rollback
* Pessimistic Locking dengan `FOR UPDATE`
* User Privilege dengan `GRANT`

---

## Implementasi View

Sistem menggunakan view untuk menyederhanakan query yang membutuhkan gabungan beberapa tabel. Contohnya:

* `view_barang_kategori`

View digunakan agar data yang ditampilkan ke aplikasi lebih mudah dibaca tanpa harus menulis query JOIN yang panjang secara berulang di file PHP.

---

## Implementasi Stored Procedure, Function, dan Trigger

Sistem menerapkan stored procedure dan function untuk membantu proses transaksi.

Contoh penerapan:

* Stored procedure untuk checkout transaksi.
* Function untuk menghitung subtotal.
* Function untuk menghitung kembalian.
* Trigger untuk mengurangi stok barang secara otomatis setelah detail transaksi ditambahkan.

Stored procedure utama yang digunakan dalam proses checkout adalah:

```text
SP_Checkout_Kasir
```

Stored procedure ini menerima data kasir, metode pembayaran, total bayar, dan data keranjang dalam format JSON. Selanjutnya, database akan memproses transaksi, menyimpan detail transaksi, menghitung total harga, menghitung kembalian, serta melakukan commit atau rollback sesuai kondisi transaksi.

---

## Implementasi Indexing

Sistem menerapkan indexing pada beberapa kolom yang sering digunakan dalam pencarian, pengurutan, login, dan relasi antar tabel.

Index yang digunakan:

```sql
CREATE INDEX idx_kategori_barang
ON barang(id_kategori);

CREATE INDEX idx_harga_barang
ON barang(harga);

CREATE UNIQUE INDEX idx_username_login
ON kasir(username);

CREATE INDEX idx_detail_transaksi
ON detail_transaksi(id_transaksi);
```

Tujuan indexing:

* Mempercepat pencarian barang berdasarkan kategori.
* Mempercepat pengurutan barang berdasarkan harga.
* Mempercepat proses login berdasarkan username.
* Mempercepat pencarian detail transaksi berdasarkan id transaksi.

---

## Manajemen User dan Privilege Database

Sistem membedakan antara pengguna aplikasi dan pengguna database.

Pengguna aplikasi disimpan di tabel:

```text
kasir
```

Sedangkan pengguna database dibuat langsung di MySQL menggunakan `CREATE USER` dan `GRANT`.

Contoh user database:

* `app_user` untuk koneksi aplikasi PHP PDO
* `owner` untuk pemilik atau administrator database
* `developer` untuk pengembangan sistem
* `backup` untuk kebutuhan backup database

Contoh privilege untuk aplikasi:

```sql
GRANT SELECT, INSERT, UPDATE, DELETE
ON tokosembako.*
TO 'app_user'@'localhost';
```

---

## Akun Login

Gunakan akun yang tersedia pada tabel `kasir` setelah database berhasil di-import.

Contoh akun:

### Kasir Admin

```text
Username : rizkyhadi
Password : rizky123
```

### Kasir

```text
Username : rizky
Password : rizky123
```

---

## Lisensi

Project ini dibuat untuk keperluan akademik sebagai tugas Mata Kuliah Manajemen Basis Data.
