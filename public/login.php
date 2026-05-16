<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
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
            color: #333;
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
            box-sizing: border-box; /* Agar padding tidak merusak lebar */
        }
        .form-group input:focus {
            border-color: #007bff;
            outline: none;
        }
        .btn-login {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1rem;
        }
        .btn-login:hover {
            background-color: #0056b3;
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
    </style>
</head>
<body>

    <div class="login-card">
        # title login page
        <h2>Toko Sembako Indojaya</h2>

        # error handling
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?php 
                    if ($_GET['error'] == 'auth') {
                        echo "Username atau password tidak valid!";
                    } else if ($_GET['error'] == 'empty') {
                        echo "Harap isi semua kolom";
                    }
                ?>
            </div>
        <?php endif; ?>


        
        <form action="proses_login.php" method="POST">
            
            # username
            <div class="form-group"> 
                <label for="username">Username</label>

                <input type="text" id="username" name="username" required placeholder="e.g John Doe">
            </div>
            
            # password
            <div class="form-group">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="e.g JohnDoe1#_">
            </div>

            <button type="submit" class="btn-login" name="btn_login">Login</button>
        </form>
    </div>

</body>
</html>