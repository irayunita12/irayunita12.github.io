<?php
require 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih - Ira Skincare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #f9f9ff;
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        text-align: center;
    }

    .thank-you-container {
        max-width: 600px;
        padding: 2rem;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    }

    .thank-you-icon {
        font-size: 4rem;
        color: #4bb543;
        margin-bottom: 1rem;
    }

    .thank-you-title {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: #333;
    }

    .thank-you-message {
        font-size: 1.1rem;
        margin-bottom: 2rem;
        color: #555;
        line-height: 1.6;
    }

    .back-to-home {
        display: inline-block;
        padding: 0.8rem 1.5rem;
        background-color: #6b9dff;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .back-to-home:hover {
        background-color: #5a89e0;
    }
    </style>
</head>

<body>
    <div class="thank-you-container">
        <div class="thank-you-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1 class="thank-you-title">Terima Kasih!</h1>
        <p class="thank-you-message">
            Pesanan Anda telah kami terima. Admin akan segera menghubungi Anda via WhatsApp untuk konfirmasi
            ketersediaan barang dan total pembayaran.
            <br><br>
            Harap periksa pesan WhatsApp Anda dalam beberapa saat.
        </p>
        <a href="index.php" class="back-to-home">
            <i class="fas fa-home"></i> Kembali ke Beranda
        </a>
    </div>
</body>

</html>