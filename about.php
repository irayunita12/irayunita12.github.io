<?php
require 'auth_check.php';
require 'db.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Ira Skincare</title>
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

    .about-container {
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

    .about-section {
        background-color: white;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
        color: #6b9dff;
    }

    .about-content {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        align-items: center;
        margin-bottom: 2rem;
    }

    .about-image {
        flex: 1;
        min-width: 300px;
    }

    .about-image img {
        width: 100%;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .about-text {
        flex: 1;
        min-width: 300px;
    }

    .about-text p {
        margin-bottom: 1rem;
        line-height: 1.6;
        color: #555;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .team-member {
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        text-align: center;
        padding: 1.5rem;
    }

    .team-photo {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 1rem;
        border: 3px solid #6b9dff;
    }

    .team-name {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .team-role {
        color: #6b9dff;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .team-desc {
        color: #666;
        font-size: 0.9rem;
    }

    .values-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .value-item {
        background-color: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    }

    .value-icon {
        font-size: 2rem;
        color: #6b9dff;
        margin-bottom: 1rem;
    }

    .value-title {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .value-desc {
        color: #666;
        line-height: 1.6;
    }

    footer {
        text-align: center;
        padding: 2rem 5%;
        background-color: white;
        margin-top: 2rem;
        border-top: 1px solid #eee;
    }

    footer p {
        margin-bottom: 0.5rem;
        color: #666;
    }

    footer a {
        color: #6b9dff;
        text-decoration: none;
        transition: color 0.3s;
    }

    footer a:hover {
        color: #5d89e0;
        text-decoration: underline;
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

        .about-content {
            flex-direction: column;
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

    <div class="about-container">
        <h1 class="page-title">Tentang <span>Kami</span></h1>

        <div class="about-section">
            <h2 class="section-title">Cerita Kami</h2>
            <div class="about-content">
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9" alt="Ira Skincare Team">
                </div>
                <div class="about-text">
                    <p>Ira Skincare didirikan pada tahun 2020 dengan misi untuk memberikan produk perawatan kulit alami
                        dan berkualitas tinggi dengan harga yang terjangkau. Kami percaya bahwa perawatan kulit yang
                        baik harus bisa dinikmati oleh semua orang.</p>
                    <p>Dengan menggunakan bahan-bahan alami pilihan dan formulasi yang dikembangkan oleh ahli
                        dermatologi, produk kami dirancang untuk berbagai jenis kulit dan masalah kulit yang berbeda.
                    </p>
                    <p>Setiap produk kami dibuat dengan penuh cinta dan perhatian terhadap detail, karena kami ingin
                        memberikan yang terbaik untuk kulit Anda.</p>
                </div>
            </div>
        </div>

        <div class="about-section">
            <h2 class="section-title">Tim Kami</h2>
            <div class="team-grid">
                <div class="team-member">
                    <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Founder" class="team-photo">
                    <h3 class="team-name">Ia Yunitar</h3>
                    <p class="team-role">Founder & CEO</p>
                    <p class="team-desc">Seorang ahli kecantikan dengan pengalaman lebih dari 10 tahun di industri
                        skincare.</p>
                </div>
                <div class="team-member">
                    <img src="https://randomuser.me/api/portraits/women/43.jpg" alt="Dermatologist" class="team-photo">
                    <h3 class="team-name">Siti Vidi</h3>
                    <p class="team-role">Dermatologist</p>
                    <p class="team-desc">Spesialis kulit yang memastikan semua formulasi produk aman dan efektif.</p>
                </div>
                <div class="team-member">
                    <img src="https://randomuser.me/api/portraits/women/22.jpg" alt="Product Development"
                        class="team-photo">
                    <h3 class="team-name">Cahaya</h3>
                    <p class="team-role">Product Development</p>
                    <p class="team-desc">Bertanggung jawab atas penelitian dan pengembangan produk baru.</p>
                </div>
                <div class="team-member">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Marketing" class="team-photo">
                    <h3 class="team-name">Wenka</h3>
                    <p class="team-role">Marketing Director</p>
                    <p class="team-desc">Membawa produk Ira Skincare ke pelanggan di seluruh Indonesia.</p>
                </div>
            </div>
        </div>

        <div class="about-section">
            <h2 class="section-title">Nilai Kami</h2>
            <div class="values-list">
                <div class="value-item">
                    <div class="value-icon"><i class="fas fa-leaf"></i></div>
                    <h3 class="value-title">Alami</h3>
                    <p class="value-desc">Kami menggunakan bahan-bahan alami berkualitas tinggi yang ramah lingkungan
                        dan aman untuk kulit.</p>
                </div>
                <div class="value-item">
                    <div class="value-icon"><i class="fas fa-flask"></i></div>
                    <h3 class="value-title">Berbasis Sains</h3>
                    <p class="value-desc">Setiap produk dikembangkan berdasarkan penelitian ilmiah dan diuji oleh ahli
                        dermatologi.</p>
                </div>
                <div class="value-item">
                    <div class="value-icon"><i class="fas fa-heart"></i></div>
                    <h3 class="value-title">Peduli</h3>
                    <p class="value-desc">Kami peduli dengan kebutuhan kulit Anda dan lingkungan sekitar kita.</p>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Ira Skincare. All rights reserved.</p>
        <p>Follow us on <a href="#">Instagram</a> | <a href="#">TikTok</a></p>
    </footer>
</body>

</html>