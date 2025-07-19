<?php
require 'auth_check.php';
require 'db.php';

// Get user ID from session
$userId = $_SESSION['user_id'];

// Query to get user's orders
$query = "SELECT o.id as order_id, o.order_date, o.total_amount, o.status, 
          GROUP_CONCAT(p.title SEPARATOR ', ') as products
          FROM orders o
          JOIN order_items oi ON o.id = oi.order_id
          JOIN products p ON oi.product_id = p.id
          WHERE o.user_id = '$userId'
          GROUP BY o.id
          ORDER BY o.order_date DESC";
$result = mysqli_query($conn, $query);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Ira Skincare</title>
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

    .order-history-container {
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

    .order-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .order-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }

    .order-id {
        font-weight: 600;
        color: #333;
    }

    .order-date {
        color: #777;
        font-size: 0.9rem;
    }

    .order-status {
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-completed {
        background-color: #d4edda;
        color: #155724;
    }

    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }

    .order-details {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .order-products {
        flex: 2;
        min-width: 300px;
    }

    .order-summary {
        flex: 1;
        min-width: 300px;
    }

    .products-title {
        font-size: 1rem;
        margin-bottom: 1rem;
        color: #333;
    }

    .product-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f5f5f5;
    }

    .product-item:last-child {
        border-bottom: none;
    }

    .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
    }

    .product-name {
        flex: 1;
        font-size: 0.9rem;
    }

    .summary-title {
        font-size: 1rem;
        margin-bottom: 1rem;
        color: #333;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .summary-label {
        color: #777;
    }

    .summary-value {
        font-weight: 500;
    }

    .summary-total {
        font-weight: 700;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }

    .order-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .action-btn {
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .view-details {
        background-color: #6b9dff;
        color: white;
        border: none;
    }

    .view-details:hover {
        background-color: #5a89e0;
    }

    .reorder {
        background-color: white;
        color: #6b9dff;
        border: 1px solid #6b9dff;
    }

    .reorder:hover {
        background-color: #ebf1ff;
    }

    .empty-orders {
        text-align: center;
        padding: 3rem;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    }

    .empty-orders i {
        font-size: 3rem;
        color: #ddd;
        margin-bottom: 1rem;
    }

    .empty-orders p {
        color: #777;
        margin-bottom: 1.5rem;
    }

    .empty-orders .btn {
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

    .empty-orders .btn:hover {
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

        .order-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .order-details {
            flex-direction: column;
        }

        .order-actions {
            justify-content: center;
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

    <div class="order-history-container">
        <h1 class="page-title">Riwayat <span>Pesanan</span></h1>

        <?php if(empty($orders)): ?>
        <div class="empty-orders">
            <i class="fas fa-box-open"></i>
            <h3>Belum ada pesanan</h3>
            <p>Mulai belanja produk skincare favorit Anda sekarang</p>
            <a href="products.php" class="btn">Belanja Sekarang</a>
        </div>
        <?php else: ?>
        <div class="order-list">
            <?php foreach($orders as $order): 
                // Determine status class
                $statusClass = '';
                if($order['status'] == 'completed') {
                    $statusClass = 'status-completed';
                } elseif($order['status'] == 'cancelled') {
                    $statusClass = 'status-cancelled';
                } else {
                    $statusClass = 'status-pending';
                }
            ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <span class="order-id">Pesanan #<?= $order['order_id'] ?></span>
                        <span class="order-date"><?= date('d M Y H:i', strtotime($order['order_date'])) ?></span>
                    </div>
                    <span class="order-status <?= $statusClass ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>

                <div class="order-details">
                    <div class="order-products">
                        <h3 class="products-title">Produk</h3>
                        <?php 
                        // Get order items details
                        $orderId = $order['order_id'];
                        $itemsQuery = "SELECT p.title, p.image_url, oi.quantity, oi.price 
                                      FROM order_items oi 
                                      JOIN products p ON oi.product_id = p.id 
                                      WHERE oi.order_id = '$orderId'";
                        $itemsResult = mysqli_query($conn, $itemsQuery);
                        $items = mysqli_fetch_all($itemsResult, MYSQLI_ASSOC);
                        
                        foreach($items as $item): 
                        ?>
                        <div class="product-item">
                            <img src="<?= htmlspecialchars($item['image_url']) ?>"
                                alt="<?= htmlspecialchars($item['title']) ?>" class="product-image">
                            <span class="product-name"><?= htmlspecialchars($item['title']) ?>
                                (<?= $item['quantity'] ?>x)</span>
                            <span class="product-price">Rp<?= number_format($item['price'], 0, ',', '.') ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-summary">
                        <h3 class="summary-title">Ringkasan</h3>
                        <div class="summary-row">
                            <span class="summary-label">Subtotal</span>
                            <span
                                class="summary-value">Rp<?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Ongkos Kirim</span>
                            <span class="summary-value">Rp10,000</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Diskon</span>
                            <span class="summary-value">Rp0</span>
                        </div>
                        <div class="summary-row summary-total">
                            <span class="summary-label">Total</span>
                            <span
                                class="summary-value">Rp<?= number_format($order['total_amount'] + 10000, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>

                <div class="order-actions">
                    <button class="action-btn view-details">Lihat Detail</button>
                    <button class="action-btn reorder">Pesan Lagi</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 Ira Skincare. All rights reserved.</p>
        <p>Follow us on <a href="#">Instagram</a> | <a href="#">TikTok</a></p>
    </footer>

    <script>
    // Function to handle reorder button click
    function handleReorder(orderId) {
        // In a real application, you would fetch the order items and add them to cart
        alert('Produk dari pesanan #' + orderId + ' akan ditambahkan ke keranjang');
        // Here you would typically make an AJAX call to get order items and add to cart
    }

    // Function to view order details
    function viewOrderDetails(orderId) {
        // In a real application, you would redirect to an order detail page
        alert('Menampilkan detail pesanan #' + orderId);
        // window.location.href = 'order_detail.php?id=' + orderId;
    }

    // Add event listeners to buttons
    document.querySelectorAll('.reorder').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.closest('.order-card').querySelector('.order-id').textContent.split(
                '#')[1];
            handleReorder(orderId);
        });
    });

    document.querySelectorAll('.view-details').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.closest('.order-card').querySelector('.order-id').textContent.split(
                '#')[1];
            viewOrderDetails(orderId);
        });
    });
    </script>
</body>

</html>