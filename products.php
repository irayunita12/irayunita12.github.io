<?php
require 'auth_check.php';
require 'db.php';

// Get all products from database
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - Ira Skincare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
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

    .products-container {
        padding: 2rem 5%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-title {
        margin-bottom: 2rem;
        color: #333;
    }

    .page-title span {
        color: #6b9dff;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .product-card {
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .product-info {
        padding: 1rem;
    }

    .product-title {
        font-size: 1rem;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .product-brand {
        font-size: 0.85rem;
        color: #777;
        margin-bottom: 0.25rem;
    }

    .product-tags {
        display: flex;
        gap: 0.5rem;
        margin: 0.5rem 0;
        flex-wrap: wrap;
    }

    .skin-tag {
        background-color: #ebf1ff;
        color: #6b9dff;
        padding: 0.2rem 0.6rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 500;
    }

    .product-price {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .current-price {
        font-weight: 700;
        color: #6b9dff;
        font-size: 1.1rem;
    }

    .original-price {
        font-size: 0.85rem;
        color: #999;
        text-decoration: line-through;
    }

    .discount {
        font-size: 0.75rem;
        background-color: #ebf1ff;
        color: #6b9dff;
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        font-weight: 600;
    }

    .buy-btn {
        width: 100%;
        padding: 0.6rem;
        background-color: #6b9dff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .buy-btn:hover {
        background-color: #5d89e0;
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

        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
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

    <div class="products-container">
        <h1 class="page-title">Semua <span>Produk</span></h1>

        <div class="products-grid" id="productsGrid">
            <?php foreach ($products as $product): 
                $skinTypes = json_decode($product['skin_type'] ?? '[]', true);
            ?>
            <div class="product-card">
                <img src="<?= htmlspecialchars($product['image_url']) ?>"
                    alt="<?= htmlspecialchars($product['title']) ?>" class="product-image">
                <div class="product-info">
                    <h3 class="product-title"><?= htmlspecialchars($product['title']) ?></h3>
                    <p class="product-brand"><?= htmlspecialchars($product['brand']) ?></p>

                    <?php if (!empty($skinTypes)): ?>
                    <div class="product-tags">
                        <?php foreach ($skinTypes as $type): ?>
                        <span class="skin-tag"><?= htmlspecialchars($type) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="product-price">
                        <span class="current-price">Rp<?= number_format($product['price'], 0, ',', '.') ?></span>
                        <?php if ($product['original_price'] > 0): ?>
                        <span
                            class="original-price">Rp<?= number_format($product['original_price'], 0, ',', '.') ?></span>
                        <?php endif; ?>
                        <?php if ($product['discount'] > 0): ?>
                        <span class="discount"><?= $product['discount'] ?>%</span>
                        <?php endif; ?>
                    </div>
                    <button class="buy-btn" onclick="addToCart(<?= htmlspecialchars(json_encode([
                            'id' => $product['id'],
                            'title' => $product['title'],
                            'price' => $product['price'],
                            'image_url' => $product['image_url']
                        ])) ?>)">
                        <i class="fas fa-shopping-cart"></i> Beli
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Ira Skincare. All rights reserved.</p>
        <p>Follow us on <a href="#">Instagram</a> | <a href="#">TikTok</a></p>
    </footer>

    <script>
    // Cart functionality
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    function addToCart(product) {
        cart.push(product);
        updateLocalStorage();
        showNotification(`${product.title} telah ditambahkan ke keranjang!`);
    }

    function updateLocalStorage() {
        localStorage.setItem("cart", JSON.stringify(cart));
    }

    function showNotification(message) {
        const notification = document.createElement("div");
        notification.className = "notification";
        notification.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <span>${message}</span>
            `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add("show");
        }, 10);

        setTimeout(() => {
            notification.classList.remove("show");
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Search functionality
    document.getElementById("searchInput").addEventListener("input", function() {
        const searchTerm = this.value.toLowerCase();
        const productCards = document.querySelectorAll(".product-card");

        productCards.forEach(card => {
            const title = card.querySelector(".product-title").textContent.toLowerCase();
            const brand = card.querySelector(".product-brand").textContent.toLowerCase();

            if (title.includes(searchTerm) || brand.includes(searchTerm)) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    });
    </script>
</body>

</html>