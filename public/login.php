<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet">
    <title>Halaman Login</title>
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
        .login-card {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
        }
        .login-card h2 {
            text-align: center;
            color: #915307;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1.2rem; /* Sedikit dilebarkan untuk ruang warning */
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
            transition: border-color 0.2s; /* Transisi agar halus */
        }
        .form-group input:focus {
            border-color: #007bff;
            outline: none;
        }
        .btn-login {
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
        .btn-login:hover {
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

        /* --- TAMBAHAN CSS UNTUK WARNING --- */
        .input-warning {
            color: #dc3545;
            font-size: 0.75rem;
            margin-top: 5px;
            display: none; /* Disembunyikan secara default */
            font-weight: 500;
        }
        /* Class ini akan ditambahkan via JS jika input ilegal */
        .input-error-border {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2 class="title">Toko Sembako Indojaya</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?php 
                    if ($_GET['error'] == 'auth') {
                        echo "Username atau password tidak valid!";
                    } else if ($_GET['error'] == 'empty') {
                        echo "Harap isi semua kolom";
                    } else if ($_GET['error'] == 'accessadmin') {
                        echo "Akses ditolak. Harap login sebagai admin.";
                    } else if($_GET['error'] == 'accesskasir') {
                        echo "Akses ditolak. Harap login sebagai kasir.";
                    } else if ($_GET['error'] == 'timeout') {
                        echo "Terlalu banyak upaya login yang gagal. Silakan coba lagi nanti.";
                    } else if ($_GET['revoked'] == 'revoked') {
                        echo "Akun Anda telah dicabut aksesnya. Silakan hubungi admin.";
                    } else {
                        echo "Terjadi kesalahan. Silakan coba lagi.";
                    } 
                ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" action="../process/proses_login.php" method="POST">
            
            <div class="form-group"> 
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="e.g John_Doe" pattern="[a-zA-Z0-9_]+">
                <div id="usernameWarning" class="input-warning">Hanya boleh berisi huruf, angka, dan garis bawah (_).</div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="e.g JohnDoe1#_">
                <div id="passwordWarning" class="input-warning">Password tidak boleh mengandung spasi atau emoji.</div>
            </div>

            <button type="submit" class="btn-login" name="btn_login">Login</button>
        </form>
    </div>

    <script>
        const inputUsername = document.getElementById('username');
        const inputPassword = document.getElementById('password');
        const warningUsername = document.getElementById('usernameWarning');
        const warningPassword = document.getElementById('passwordWarning');
        const loginForm = document.getElementById('loginForm');

        // Regex Username: Hanya huruf, angka, dan underscore
        const regexUsername = /^[a-zA-Z0-9_]+$/;
        
        // Regex Password: Hanya karakter standar keyboard (ASCII yang terlihat). 
        // Memblokir spasi (\x20) dan emoji/karakter aneh di luar rentang \x21 hingga \x7E.
        const regexPassword = /^[\x21-\x7E]+$/;

        // Fungsi umum untuk mengecek input
        function validateInput(inputElement, warningElement, regex) {
            const value = inputElement.value;
            
            // Jika kosong, hilangkan warning (biarkan atribut 'required' HTML yang bekerja nanti)
            if (value === "") {
                warningElement.style.display = 'none';
                inputElement.classList.remove('input-error-border');
                return true;
            }

            // Jika nilai TIDAK sesuai dengan aturan Regex (ada karakter ilegal)
            if (!regex.test(value)) {
                warningElement.style.display = 'block';
                inputElement.classList.add('input-error-border');
                return false;
            } else {
                // Jika user menghapus karakter ilegal dan string kembali valid
                warningElement.style.display = 'none';
                inputElement.classList.remove('input-error-border');
                return true;
            }
        }

        // Jalankan pengecekan setiap kali user mengetik sesuatu
        inputUsername.addEventListener('input', function() {
            validateInput(inputUsername, warningUsername, regexUsername);
        });

        inputPassword.addEventListener('input', function() {
            validateInput(inputPassword, warningPassword, regexPassword);
        });

        // Mencegah form dikirim jika masih ada karakter ilegal di dalam kotak
        loginForm.addEventListener('submit', function(e) {
            const isUsernameValid = validateInput(inputUsername, warningUsername, regexUsername);
            const isPasswordValid = validateInput(inputPassword, warningPassword, regexPassword);
            
            if (!isUsernameValid || !isPasswordValid) {
                e.preventDefault(); // Batalkan pengiriman data
            }
        });
    </script>
</body>
</html>