<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet">
    <title>Halaman Register</title>
    <style>
        body {
            background-image: url('asset/login_background.png');
            background-repeat: no-repeat;
            background-size: cover; 
            background-attachment: fixed; 
            font-family: "Google Sans", sans-serif;
            background-color: #e4eb9d;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-card {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px; 
        }
        .register-card h2 {
            text-align: center;
            color: #915307;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; 
        }
        .form-group input:focus {
            border-color: #007bff;
            outline: none;
        }
        .btn-register {
            width: 100%;
            padding: 10px;
            background-color: #8da750;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1rem;
        }
        .btn-register:hover {
            background-color: #537c2e;
        }
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }
        .success-message {
            color: #155724;
            background-color: #d4edda;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }
        .login-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #555;
            text-decoration: none;
        }
        .login-link:hover {
            color: #8da750;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="register-card">
        <!--title register page -->
        <h2 class="title">Daftar Akun Indojaya</h2>

        <!-- success handling -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            <div class="success-message">
                Registrasi berhasil! Silakan login.
            </div>
        <?php endif; ?>

        <!-- error handling -->
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?php 
                    if ($_GET['error'] == 'empty') {
                        echo "Harap isi semua kolom!";
                    } else if ($_GET['error'] == 'weak') {
                        echo "Password harus minimal 8 karakter dan mengandung huruf besar, huruf kecil, angka, serta simbol!";
                    } else if ($_GET['error'] == 'username') {
                        echo "Username hanya boleh huruf, angka, dan underscore!";
                    } else if ($_GET['error'] == 'toolong') {
                        echo "Input terlalu panjang!";
                    } else if ($_GET['error'] == 'locked') {
                        echo "Terlalu banyak percobaan registrasi. Coba lagi dalam 5 menit!";
                    } else if ($_GET['error'] == 'server') {
                        echo "Terjadi kesalahan server!";
                    } else if ($_GET['error'] == 'mismatch') {
                        echo "Konfirmasi password tidak cocok!";
                    } else if ($_GET['error'] == 'exists') {
                        echo "Username sudah terdaftar, gunakan yang lain!";
                    }
                ?>
            </div>
        <?php endif; ?>

        <form action="../process/proses_register.php" method="POST">
            
            <!-- nama lengkap -->
            <div class="form-group"> 
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" required placeholder="e.g John Doe">
            </div>

            <!-- username -->
            <div class="form-group"> 
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="e.g johndoe123">
            </div>
            
            <!-- password -->
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Buat password yang kuat">
            </div>

            <!-- konfirmasi password -->
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Ulangi password di atas">
            </div>

            <button type="submit" class="btn-register" name="btn_register">Daftar</button>

            <!-- link ke login -->
            <a href="login.php" class="login-link">Sudah punya akun? Login di sini</a>
        </form>
    </div>

</body>
</html>