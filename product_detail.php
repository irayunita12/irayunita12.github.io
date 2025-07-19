<?php
require 'auth_check.php';
require 'db.php';

// Ambil ID produk dari URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    header("Location: products.php");
    exit;
}

// Query untuk mendapatkan detail produk
$query = "SELECT * FROM products WHERE id = $productId";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: products.php");
    exit;
}

// Decode skin_type JSON
$skinTypes = json_decode($product['skin_type'] ?? '[]', true);

// Query untuk mendapatkan produk terkait (dari brand yang sama)
$relatedQuery = "SELECT * FROM products WHERE brand = '{$product['brand']}' AND id != $productId LIMIT 4";
$relatedResult = mysqli_query($conn, $relatedQuery);
$relatedProducts = mysqli_fetch_all($relatedResult, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['title']) ?> - Ira Skincare</title>
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

    .search-container {
        position: relative;
        width: 30%;
    }

    .search-bar {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #ddd;
        border-radius: 60px;
        outline: none;
        transition: border 0.3s;
        font-size: 0.9rem;
    }

    .search-bar:focus {
        border-color: #6b9dff;
    }

    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #777;
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

    .product-detail-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 5%;
    }

    .breadcrumb {
        margin-bottom: 1rem;
        font-size: 0.9rem;
        color: #666;
    }

    .breadcrumb a {
        color: #6b9dff;
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .product-detail {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .product-gallery {
        flex: 1;
        min-width: 300px;
    }

    .main-image {
        width: 100%;
        height: 400px;
        object-fit: contain;
        background: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 1rem;
    }

    .thumbnail-container {
        display: flex;
        gap: 1rem;
    }

    .thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s;
    }

    .thumbnail:hover {
        border-color: #6b9dff;
    }

    .product-info {
        flex: 1;
        min-width: 300px;
    }

    .product-title {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .product-brand {
        font-size: 1.2rem;
        color: #6b9dff;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .product-meta {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        color: #666;
        font-size: 0.9rem;
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        color: #ffb400;
    }

    .product-price-container {
        margin: 1.5rem 0;
    }

    .current-price {
        font-size: 1.8rem;
        font-weight: 700;
        color: #6b9dff;
    }

    .original-price {
        font-size: 1.2rem;
        color: #999;
        text-decoration: line-through;
        margin-left: 0.5rem;
    }

    .discount {
        font-size: 0.9rem;
        background-color: #ebf1ff;
        color: #6b9dff;
        padding: 0.2rem 0.6rem;
        border-radius: 4px;
        font-weight: 600;
        margin-left: 0.5rem;
    }

    .product-description {
        margin: 1.5rem 0;
        line-height: 1.6;
        color: #555;
    }

    .product-tags {
        display: flex;
        gap: 0.5rem;
        margin: 1rem 0;
        flex-wrap: wrap;
    }

    .skin-tag {
        background-color: #ebf1ff;
        color: #6b9dff;
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 1.5rem 0;
    }

    .quantity-btn {
        width: 30px;
        height: 30px;
        border: 1px solid #ddd;
        background: none;
        font-size: 1rem;
        cursor: pointer;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quantity-input {
        width: 50px;
        height: 30px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .add-to-cart-btn,
    .buy-now-btn {
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .add-to-cart-btn {
        background-color: #6b9dff;
        color: white;
    }

    .add-to-cart-btn:hover {
        background-color: #5a89e0;
    }

    .buy-now-btn {
        background-color: #333;
        color: white;
    }

    .buy-now-btn:hover {
        background-color: #222;
    }

    .related-products {
        margin: 3rem 0;
    }

    .section-title {
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
        color: #333;
    }

    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .related-product {
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .related-product:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .related-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .related-info {
        padding: 1rem;
    }

    .related-title {
        font-size: 1rem;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .related-price {
        font-weight: 700;
        color: #6b9dff;
        font-size: 1.1rem;
    }

    .notification {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        background-color: #4bb543;
        color: white;
        padding: 12px 20px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        opacity: 0;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .notification.show {
        transform: translateX(-50%) translateY(0);
        opacity: 1;
    }

    .notification i {
        font-size: 1.2rem;
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

        .search-container {
            order: 3;
            width: 100%;
        }

        .menu-container {
            order: 2;
            margin-left: auto;
        }

        .main-image {
            height: 300px;
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

        <div class="search-container">
            <input type="text" class="search-bar" placeholder="Cari produk skincare..." id="searchInput">
            <span class="search-icon">🔍</span>
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

    <div class="product-detail-container">
        <div class="breadcrumb">
            <a href="index.php">Beranda</a> &gt;
            <a href="products.php">Produk</a> &gt;
            <span><?= htmlspecialchars($product['title']) ?></span>
        </div>

        <div class="product-detail">
            <div class="product-gallery">
                <img src="<?= htmlspecialchars($product['image_url']) ?>"
                    alt="<?= htmlspecialchars($product['title']) ?>" class="main-image" id="mainImage">
                <div class="thumbnail-container">
                    <img src="<?= htmlspecialchars($product['image_url']) ?>"
                        alt="<?= htmlspecialchars($product['title']) ?>" class="thumbnail" onclick="changeImage(this)">
                    <!-- Jika ada gambar tambahan bisa ditambahkan di sini -->
                </div>
            </div>

            <div class="product-info">
                <h1 class="product-title"><?= htmlspecialchars($product['title']) ?></h1>
                <p class="product-brand"><?= htmlspecialchars($product['brand']) ?></p>

                <div class="product-meta">
                    <span>Kategori: <?= htmlspecialchars($product['category']) ?></span>
                    <span>Tahun: <?= htmlspecialchars($product['year']) ?></span>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <span>4.8 (120 ulasan)</span>
                    </div>
                </div>

                <div class="product-price-container">
                    <span class="current-price">Rp<?= number_format($product['price'], 0, ',', '.') ?></span>
                    <?php if ($product['original_price'] > 0): ?>
                    <span class="original-price">Rp<?= number_format($product['original_price'], 0, ',', '.') ?></span>
                    <span class="discount"><?= $product['discount'] ?>%</span>
                    <?php endif; ?>
                </div>

                <div class="product-description">
                    <p>Deskripsi produk akan ditampilkan di sini. Anda dapat menambahkan deskripsi panjang tentang
                        produk skincare ini, manfaatnya, bahan-bahan yang digunakan, dan cara penggunaannya.</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in dui mauris. Vivamus hendrerit
                        arcu sed erat molestie vehicula. Sed auctor neque eu tellus rhoncus ut eleifend nibh porttitor.
                    </p>
                </div>

                <?php if (!empty($skinTypes)): ?>
                <div>
                    <h4>Cocok untuk jenis kulit:</h4>
                    <div class="product-tags">
                        <?php foreach ($skinTypes as $type): ?>
                        <span class="skin-tag"><?= htmlspecialchars($type) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="quantity-selector">
                    <label for="quantity">Jumlah:</label>
                    <button class="quantity-btn" onclick="changeQuantity(-1)">-</button>
                    <input type="number" id="quantity" class="quantity-input" value="1" min="1" max="10">
                    <button class="quantity-btn" onclick="changeQuantity(1)">+</button>
                </div>

                <div class="action-buttons">
                    <button class="add-to-cart-btn" onclick="addToCart()">
                        <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                    </button>
                    <button class="buy-now-btn" onclick="buyNow()">
                        <i class="fas fa-bolt"></i> Beli Sekarang
                    </button>
                </div>
            </div>
        </div>

        <?php if (!empty($relatedProducts)): ?>
        <div class="related-products">
            <h2 class="section-title">Produk Lain dari <?= htmlspecialchars($product['brand']) ?></h2>
            <div class="related-grid">
                <?php foreach ($relatedProducts as $related): ?>
                <div class="related-product"
                    onclick="window.location.href='product_detail.php?id=<?= $related['id'] ?>'">
                    <img src="<?= htmlspecialchars($related['image_url']) ?>"
                        alt="<?= htmlspecialchars($related['title']) ?>" class="related-image">
                    <div class="related-info">
                        <h3 class="related-title"><?= htmlspecialchars($related['title']) ?></h3>
                        <div class="related-price">Rp<?= number_format($related['price'], 0, ',', '.') ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 Ira Skincare. All rights reserved.</p>
        <p>Follow us on <a href="#">Instagram</a> | <a href="#">TikTok</a></p>
    </footer>

    <script>
    // Fungsi untuk mengubah gambar utama saat thumbnail diklik
    function changeImage(thumbnail) {
        const mainImage = document.getElementById('mainImage');
        mainImage.src = thumbnail.src;
    }

    // Fungsi untuk mengubah jumlah produk
    function changeQuantity(change) {
        const quantityInput = document.getElementById('quantity');
        let newValue = parseInt(quantityInput.value) + change;

        if (newValue < 1) newValue = 1;
        if (newValue > 10) newValue = 10;

        quantityInput.value = newValue;
    }

    // Fungsi untuk menambahkan produk ke keranjang
    function addToCart() {
        const quantity = parseInt(document.getElementById('quantity').value);
        const product = <?= json_encode([
            'id' => $product['id'],
            'title' => $product['title'],
            'price' => $product['price'],
            'image_url' => $product['image_url']
        ]) ?>;

        // Ambil keranjang dari localStorage atau buat baru jika kosong
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        // Tambahkan produk sebanyak quantity
        for (let i = 0; i < quantity; i++) {
            cart.push(product);
        }

        // Simpan kembali ke localStorage
        localStorage.setItem('cart', JSON.stringify(cart));

        // Tampilkan notifikasi
        showNotification(`${product.title} telah ditambahkan ke keranjang!`);
    }

    // Fungsi untuk beli sekarang
    function buyNow() {
        addToCart();
        window.location.href = 'checkout.php';
    }

    // Fungsi untuk menampilkan notifikasi
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
    </script>
</body>

</html>