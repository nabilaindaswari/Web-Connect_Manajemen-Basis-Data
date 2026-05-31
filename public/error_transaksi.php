<?php
// Menerima variabel dari halaman sebelumnya, atau gunakan default jika kosong
$judul_error = $judul_error ?? "Pemberitahuan Sistem";
$pesan_error = $pesan_error ?? "Terjadi kesalahan atau halaman tidak valid.";
$link_kembali = $link_kembali ?? "../public/kasir_home.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($judul_error) ?> - Toko Sembako Indojaya</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --font-main: 'Inter', sans-serif;
            --bg-body: #E5D3B3; /* Mengikuti tema utama */
            --bg-card: #ffffff;
            --text-dark: #333333;
            --text-gray: #666666;
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
            padding: 20px;
        }

        .error-card {
            background-color: var(--bg-card);
            width: 100%;
            max-width: 420px;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        .icon-wrapper {
            width: 64px;
            height: 64px;
            background-color: #fdf2f2;
            color: #e53e3e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        h1 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--text-dark);
        }

        p {
            font-size: 15px;
            color: var(--text-gray);
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .btn-back {
            display: inline-block;
            width: 100%;
            background-color: var(--btn-bg);
            color: #ffffff;
            text-decoration: none;
            padding: 12px 0;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            transition: opacity 0.2s;
        }

        .btn-back:hover {
            opacity: 0.9;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="error-card">
        <div class="icon-wrapper">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        
        <h1><?= htmlspecialchars($judul_error) ?></h1>
        <p><?= htmlspecialchars($pesan_error) ?></p>
        
        <a href="<?= htmlspecialchars($link_kembali) ?>" class="btn-back">Kembali</a>
    </div>

</body>
</html>