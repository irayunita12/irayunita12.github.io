<?php
require 'auth_check.php';
require 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Validate inputs
    $errors = [];
    if (empty($name)) {
        $errors[] = 'Nama wajib diisi';
    }
    if (empty($email)) {
        $errors[] = 'Email wajib diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid';
    }
    if (empty($subject)) {
        $errors[] = 'Subjek wajib diisi';
    }
    if (empty($message)) {
        $errors[] = 'Pesan wajib diisi';
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        $query = "INSERT INTO contacts (name, email, subject, message, created_at) 
                  VALUES ('$name', '$email', '$subject', '$message', NOW())";
        if (mysqli_query($conn, $query)) {
            $success = 'Pesan Anda telah terkirim! Kami akan segera menghubungi Anda.';
        } else {
            $errors[] = 'Gagal mengirim pesan: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - Ira Skincare</title>
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
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 5%;
        background-color: white;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .logo-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo-img {
        height: 40px;
        width: auto;
    }

    .logo-text {
        font-size: 1.5rem;
        font-weight: 700;
        color: #6b9dff;
    }

    .logo-text span {
        color: #333;
        font-weight: 400;
    }

    .menu-container {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .auth-button {
        padding: 0.5rem 1.25rem;
        background-color: transparent;
        border: 1px solid #6b9dff;
        color: #6b9dff;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .auth-button:hover {
        background-color: #6b9dff;
        color: white;
    }

    .contact-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 5%;
    }

    .page-title {
        font-size: 1.8rem;
        margin-bottom: 2rem;
        color: #333;
    }

    .page-title span {
        color: #6b9dff;
    }

    .contact-content {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
    }

    .contact-info {
        flex: 1;
        min-width: 300px;
    }

    .contact-form {
        flex: 1;
        min-width: 300px;
    }

    .info-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .info-title {
        font-size: 1.2rem;
        margin-bottom: 1rem;
        color: #333;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .info-icon {
        color: #6b9dff;
        font-size: 1.2rem;
        margin-top: 0.2rem;
    }

    .info-text {
        flex: 1;
    }

    .info-text h4 {
        margin-bottom: 0.3rem;
        color: #333;
    }

    .info-text p {
        color: #666;
        font-size: 0.9rem;
    }

    .form-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #333;
        font-weight: 500;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: all 0.3s;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        border-color: #6b9dff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(107, 157, 255, 0.1);
    }

    .form-group textarea {
        min-height: 150px;
        resize: vertical;
    }

    .submit-btn {
        padding: 0.8rem 1.5rem;
        background-color: #6b9dff;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 1rem;
    }

    .submit-btn:hover {
        background-color: #5a89e0;
    }

    .alert {
        padding: 0.8rem 1rem;
        margin-bottom: 1.5rem;
        border-radius: 6px;
        font-size: 0.9rem;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    .social-links {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }

    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: #f0f4ff;
        color: #6b9dff;
        border-radius: 50%;
        font-size: 1.2rem;
        transition: all 0.3s;
    }

    .social-link:hover {
        background-color: #6b9dff;
        color: white;
    }

    @media (max-width: 768px) {
        .header {
            flex-wrap: wrap;
            gap: 1rem;
        }

        .logo-container {
            order: 1;
        }

        .menu-container {
            order: 2;
            margin-left: auto;
        }
    }
    </style>
</head>

<body>
    <header class="header">
        <div class="logo-container">
            <img src="logo.png" alt="Ira Skincare Logo" class="logo-img">
            <div class="logo-text">Ira<span>_Skincare</span></div>
        </div>

        <div class="menu-container">
            <?php if(isset($_SESSION['user_id'])): ?>
            <a href="profile.php" class="auth-button">
                <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['nama']) ?>
            </a>
            <a href="logout.php" class="auth-button">Logout</a>
            <?php else: ?>
            <a href="login.php" class="auth-button">LOGIN</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="contact-container">
        <h1 class="page-title">Hubungi <span>Kami</span></h1>

        <?php if(!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach($errors as $error): ?>
            <p><?= $error ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if(isset($success)): ?>
        <div class="alert alert-success">
            <?= $success ?>
        </div>
        <?php endif; ?>

        <div class="contact-content">
            <div class="contact-info">
                <div class="info-card">
                    <h3 class="info-title"><i class="fas fa-info-circle"></i> Informasi Kontak</h3>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt info-icon"></i>
                        <div class="info-text">
                            <h4>Alamat</h4>
                            <p>Jl. Awang Long No. 45, Bontang Lestari, Bontang Utara, Kalimantan Timur, Indonesia</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone-alt info-icon"></i>
                        <div class="info-text">
                            <h4>Telepon/WhatsApp</h4>
                            <p>+62 812 3456 7890</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope info-icon"></i>
                        <div class="info-text">
                            <h4>Email</h4>
                            <p>info@iraskincare-bontang.com</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock info-icon"></i>
                        <div class="info-text">
                            <h4>Jam Operasional</h4>
                            <p>Senin - Jumat: 08:00 - 17:00 WITA<br>Sabtu: 08:00 - 15:00 WITA</p>
                        </div>
                    </div>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>

                <div class="info-card">
                    <h3 class="info-title"><i class="fas fa-map-marked-alt"></i> Lokasi Kami</h3>
                    <div
                        style="width: 100%; height: 300px; background-color: #eee; border-radius: 6px; overflow: hidden;">
                        <!-- Google Maps Embed for Bontang -->
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.682245943574!2d117.5008143147539!3d0.132722999966691!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3206e7c1e7a3a3a5%3A0x5e7a4d2e5a0e4b0!2sBontang%2C%20Kota%20Bontang%2C%20Kalimantan%20Timur!5e0!3m2!1sen!2sid!4v1620000000000!5m2!1sen!2sid"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer>
        <p>&copy; 2025 Ira Skincare Bontang. All rights reserved.</p>
        <p>Follow us on <a href="#">Instagram</a> | <a href="#">TikTok</a></p>
    </footer>
</body>

</html>