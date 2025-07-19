<?php
require 'auth_check.php';
require 'db.php';

// Get user ID from session
$userId = $_SESSION['user_id'];

// Query to get user's wishlist items
$query = "SELECT p.* FROM wishlist w 
          JOIN products p ON w.product_id = p.id 
          WHERE w.user_id = '$userId' 
          ORDER BY w.created_at DESC";
$result = mysqli_query($conn, $query);
$wishlistItems = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist - Ira Skincare</title>
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

    .wishlist-container {
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

    .wishlist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .wishlist-item {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
    }

    .wishlist-item:hover {
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
        position: relative;
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

    .product-price {
        font-weight: 700;
        color: #6b9dff;
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }

    .product-actions {
        display: flex;
        gap: 0.5rem;
    }

    .add-to-cart-btn,
    .remove-btn {
        flex: 1;
        padding: 0.5rem;
        border: none;
        border-radius: 4px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
    }

    .add-to-cart-btn {
        background-color: #6b9dff;
        color: white;
    }

    .add-to-cart-btn:hover {
        background-color: #5a89e0;
    }

    .remove-btn {
        background-color: #ff6b6b;
        color: white;
    }

    .remove-btn:hover {
        background-color: #e55a5a;
    }

    .empty-wishlist {
        text-align: center;
        padding: 3rem;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        grid-column: 1 / -1;
    }

    .empty-wishlist i {
        font-size: 3rem;
        color: #ddd;
        margin-bottom: 1rem;
    }

    .empty-wishlist p {
        color: #777;
        margin-bottom: 1.5rem;
    }

    .empty-wishlist .btn {
        padding: 0.8rem 1.5rem;
        background-color: #6b9dff;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .empty-wishlist .btn:hover {
        background-color: #5a89e0;
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

        .wishlist-grid {
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

    <div class="wishlist-container">
        <h1 class="page-title">Wishlist <span>Produk</span></h1>

        <div class="wishlist-grid">
            <?php if(empty($wishlistItems)): ?>
            <div class="empty-wishlist">
                <i class="fas fa-heart"></i>
                <h3>Wishlist Anda kosong</h3>
                <p>Tambahkan produk favorit Anda ke wishlist untuk melihatnya di sini</p>
                <a href="products.php" class="btn">Jelajahi Produk</a>
            </div>
            <?php else: ?>
            <?php foreach($wishlistItems as $item): ?>
            <div class="wishlist-item" data-id="<?= $item['id'] ?>">
                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['title']) ?>"
                    class="product-image">
                <div class="product-info">
                    <h3 class="product-title"><?= htmlspecialchars($item['title']) ?></h3>
                    <p class="product-brand"><?= htmlspecialchars($item['brand']) ?></p>
                    <p class="product-price">Rp<?= number_format($item['price'], 0, ',', '.') ?></p>
                    <div class="product-actions">
                        <button class="add-to-cart-btn" onclick="addToCart(<?= htmlspecialchars(json_encode([
                            'id' => $item['id'],
                            'title' => $item['title'],
                            'price' => $item['price'],
                            'image_url' => $item['image_url']
                        ])) ?>)">
                            <i class="fas fa-shopping-cart"></i> Beli
                        </button>
                        <button class="remove-btn" onclick="removeFromWishlist(<?= $item['id'] ?>)">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Ira Skincare. All rights reserved.</p>
        <p>Follow us on <a href="#">Instagram</a> | <a href="#">TikTok</a></p>
    </footer>

    <script>
    // Function to add product to cart
    function addToCart(product) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart.push(product);
        localStorage.setItem('cart', JSON.stringify(cart));
        showNotification(`${product.title} telah ditambahkan ke keranjang!`);
    }

    // Function to remove product from wishlist
    function removeFromWishlist(productId) {
        if (confirm('Apakah Anda yakin ingin menghapus produk ini dari wishlist?')) {
            fetch('wishlist_action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=remove&product_id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove item from DOM
                        document.querySelector(`.wishlist-item[data-id="${productId}"]`).remove();
                        showNotification('Produk telah dihapus dari wishlist');

                        // If no more items, show empty state
                        if (document.querySelectorAll('.wishlist-item').length === 0) {
                            document.querySelector('.wishlist-grid').innerHTML = `
                            <div class="empty-wishlist">
                                <i class="fas fa-heart"></i>
                                <h3>Wishlist Anda kosong</h3>
                                <p>Tambahkan produk favorit Anda ke wishlist untuk melihatnya di sini</p>
                                <a href="products.php" class="btn">Jelajahi Produk</a>
                            </div>
                        `;
                        }
                    } else {
                        showNotification('Gagal menghapus produk: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan saat menghapus produk');
                });
        }
    }

    // Function to show notification
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