<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$nama = $_SESSION['nama'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - ira Beauty</title>z
    <style>
    body {
        font-family: Arial, sans-serif;
        background: #fff0f5;
        margin: 0;
        padding: 0;
    }

    .dashboard {
        max-width: 800px;
        margin: 50px auto;
        padding: 30px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #d84e8b;
        margin-bottom: 20px;
    }

    .info {
        margin-bottom: 30px;
    }

    .btn {
        display: inline-block;
        background: #d84e8b;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 6px;
        margin-right: 10px;
    }

    .btn:hover {
        background: #c3477e;
    }
    </style>
</head>

<body>

    <div class="dashboard">
        <h2>Halo, <?= htmlspecialchars($nama); ?> 👋</h2>
        <div class="info">
            <p>Email: <?= htmlspecialchars($email); ?></p>
        </div>
        <a href="index.php" class="btn">Kembali ke Halaman Utama</a>
        <a href="logout.php" class="btn">Logout</a>
    </div>

</body>

</html>