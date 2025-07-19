<?php
require 'auth_check.php';
require 'db.php';

// Get search query from GET parameter
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// Initialize variables
$products = [];
$message = '';

// If search term is provided, search in database
if (!empty($searchTerm)) {
    // Sanitize the search term to prevent SQL injection
    $searchTerm = mysqli_real_escape_string($conn, $searchTerm);
    
    // Search query - look in title, brand, and category
    $query = "SELECT * FROM products 
              WHERE title LIKE '%$searchTerm%' 
              OR brand LIKE '%$searchTerm%' 
              OR category LIKE '%$searchTerm%'
              ORDER BY id DESC";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $message = "Error searching products: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Produk - Ira Skincare</title>
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

    .search-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 5%;
    }

    .search-header {
        margin-bottom: 2rem;
    }

    .search-title {
        font-size: 1.8rem;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .search-term {
        color: #6b9dff;
    }

    .results-count {
        color: #666;
        font-size: 1rem;
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
    }

    .buy-btn:hover {
        background-color: #5d89e0;
    }

    .no-results {
        text-align: center;
        padding: 3rem;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        grid-column: 1 / -1;
    }

    .no-results i {
        font-size: 3rem;
        color: #ddd;
        margin-bottom: 1rem;
    }

    .no-results p {
        color: #777;
        margin-bottom: 1.5rem;
    }

    .no-results .btn {
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

    .no-results .btn:hover {
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

    <div class="search-container">
        <div class="search-header">
            <h1 class="search-title">Hasil Pencarian: <span
                    class="search-term"><?= htmlspecialchars($searchTerm) ?></span></h1>
            <p class="results-count">Ditemukan <?= count($products) ?> produk</p>
        </div>

        <div class="products-grid">
            <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="<?= htmlspecialchars($product['image_url']) ?>"
                    alt="<?= htmlspecialchars($product['title']) ?>" class="product-image"
                    onclick="window.location.href='product_detail.php?id=<?= $product['id'] ?>'">
                <div class="product-info">
                    <h3 class="product-title"><?= htmlspecialchars($product['title']) ?></h3>
                    <p class="product-brand"><?= htmlspecialchars($product['brand']) ?></p>
                    <div class="product-price">
                        <span class="current-price">Rp<?= number_format($product['price'], 0, ',', '.') ?></span>
                        <?php if (isset($product['original_price']) && $product['original_price'] > 0): ?>
                        <span
                            class="original-price">Rp<?= number_format($product['original_price'], 0, ',', '.') ?></span>
                        <span class="discount"><?= $product['discount'] ?>%</span>
                        <?php endif; ?>
                    </div>
                    <button class="buy-btn" onclick="addToCart(<?= $product['id'] ?>)">
                        <i class="fas fa-shopping-cart"></i> Beli
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h3>Tidak ada produk yang ditemukan</h3>
                <p>Kami tidak dapat menemukan produk dengan kata kunci "<?= htmlspecialchars($searchTerm) ?>"</p>
                <a href="products.php" class="btn">Lihat Semua Produk</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Ira Skincare. All rights reserved.</p>
        <p>Follow us on <a href="#">Instagram</a> | <a href="#">TikTok</a></p>
    </footer>

    <script>
    // Function to add product to cart
    function addToCart(productId) {
        // In a real application, you would make an AJAX call to add to cart
        // For now, we'll just show an alert
        alert('Produk dengan ID ' + productId + ' akan ditambahkan ke keranjang');

        // Prevent the click event from bubbling up to the product card
        event.stopPropagation();
    }

    // Add click event to product cards
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function() {
            const productId = this.querySelector('.buy-btn').getAttribute('onclick').match(/\d+/)[0];
            window.location.href = 'product_detail.php?id=' + productId;
        });
    });
    </script>
</body>

</html>