<?php
session_start();
require 'db.php';

$error = '';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Validasi
    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi!';
    } else {
        // Cek email di database
        $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Simpan data user di session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                  $_SESSION['profile_picture'] = $user['profile_picture'] ?? 'default-profile.png'; // Gunakan default jika tidak ada
                
                // Redirect berdasarkan role
                header("Location: " . ($user['role'] === 'admin' ? 'admin_dashboard.php' : 'index.php'));
                exit;
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Email tidak terdaftar!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ira Beauty</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: linear-gradient(135deg, #f5f9ff, #eceff9);
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
    }

    .login-container {
        background-color: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(107, 142, 255, 0.1);
        width: 100%;
        max-width: 450px;
        padding: 40px;
        text-align: center;
    }

    .logo {
        margin-bottom: 30px;
    }

    .logo img {
        width: 80px;
        height: 80px;
        object-fit: contain;
    }

    .logo h1 {
        color: #333;
        font-size: 28px;
        margin-top: 10px;
    }

    .logo h1 span {
        color: #6b8cff;
    }

    .input-group {
        margin-bottom: 20px;
        text-align: left;
    }

    .input-group label {
        display: block;
        margin-bottom: 8px;
        color: #555;
        font-weight: 500;
    }

    .input-group input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s;
    }

    .input-group input:focus {
        border-color: #6b8cff;
        box-shadow: 0 0 0 3px rgba(107, 142, 255, 0.2);
        outline: none;
    }

    .login-btn {
        width: 100%;
        padding: 12px;
        background-color: #6b8cff;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 10px;
    }

    .login-btn:hover {
        background-color: #5a7ae5;
    }

    .error-message {
        color: #ff4444;
        background-color: #ffebee;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .register-link {
        margin-top: 20px;
        color: #666;
        font-size: 14px;
    }

    .register-link a {
        color: #6b8cff;
        text-decoration: none;
        font-weight: 500;
    }

    .register-link a:hover {
        text-decoration: underline;
    }

    .divider {
        display: flex;
        align-items: center;
        margin: 20px 0;
        color: #999;
    }

    .divider::before,
    .divider::after {
        content: "";
        flex: 1;
        border-bottom: 1px solid #eee;
    }

    .divider::before {
        margin-right: 10px;
    }

    .divider::after {
        margin-left: 10px;
    }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <img src="logo.png" alt="Ira Beauty Logo">
            <h1>Ira<span>Beauty</span></h1>
        </div>

        <?php if ($error): ?>
        <div class="error-message"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Masukkan email Anda" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
            </div>

            <button type="submit" name="login" class="login-btn">Masuk</button>
        </form>

        <div class="divider">atau</div>

        <p class="register-link">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
    </div>
</body>

</html>