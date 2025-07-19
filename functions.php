<?php
// functions.php - Common utility functions for Ira Beauty application

require_once 'db.php';

/**
 * Redirect to a specified URL with optional status code
 * @param string $url The URL to redirect to
 * @param int $statusCode HTTP status code (default: 302)
 */
function redirect($url, $statusCode = 302) {
    header('Location: ' . $url, true, $statusCode);
    exit();
}

/**
 * Check if user is logged in
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user has admin role
 * @return bool True if user is admin, false otherwise
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Format price with currency symbol
 * @param float $price The price to format
 * @return string Formatted price string
 */
function formatPrice($price) {
    return 'Rp' . number_format($price, 0, ',', '.');
}

/**
 * Get current URL with query parameters
 * @return string Current URL
 */
function currentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Generate a random string
 * @param int $length Length of the string to generate
 * @return string Random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

/**
 * Upload file with validation
 * @param array $file $_FILES array element
 * @param string $targetDir Directory to upload to
 * @param array $allowedTypes Allowed MIME types
 * @param int $maxSize Maximum file size in bytes
 * @return array ['success' => bool, 'message' => string, 'path' => string]
 */
function uploadFile($file, $targetDir, $allowedTypes = ['image/jpeg', 'image/png'], $maxSize = 2097152) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }

    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }

    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File too large'];
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $extension;
    $targetPath = $targetDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }

    return ['success' => true, 'message' => 'File uploaded successfully', 'path' => $targetPath];
}

/**
 * Get user data by ID
 * @param int $userId User ID
 * @return array|null User data or null if not found
 */
function getUserById($userId) {
    global $conn;
    $userId = (int)$userId;
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Get product data by ID
 * @param int $productId Product ID
 * @return array|null Product data or null if not found
 */
function getProductById($productId) {
    global $conn;
    $productId = (int)$productId;
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Add product to user's cart in session
 * @param int $productId Product ID
 * @param int $quantity Quantity to add
 */
function addToCart($productId, $quantity = 1) {
    $product = getProductById($productId);
    if (!$product) return false;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    for ($i = 0; $i < $quantity; $i++) {
        $_SESSION['cart'][] = [
            'id' => $product['id'],
            'title' => $product['title'],
            'price' => $product['price'],
            'image_url' => $product['image_url']
        ];
    }

    return true;
}

/**
 * Remove product from user's cart in session
 * @param int $productId Product ID
 * @param int $quantity Quantity to remove
 */
function removeFromCart($productId, $quantity = 1) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return false;
    }

    $productId = (int)$productId;
    $removed = 0;

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $productId && $removed < $quantity) {
            unset($_SESSION['cart'][$key]);
            $removed++;
        }
    }

    // Reindex array
    $_SESSION['cart'] = array_values($_SESSION['cart']);

    return $removed > 0;
}

/**
 * Get cart total amount
 * @return float Total amount
 */
function getCartTotal() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }

    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'];
    }

    return $total;
}

/**
 * Get cart item count
 * @return int Item count
 */
function getCartItemCount() {
    return isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
}

/**
 * Add product to user's wishlist
 * @param int $userId User ID
 * @param int $productId Product ID
 * @return bool True on success, false on failure
 */
function addToWishlist($userId, $productId) {
    global $conn;
    
    // Check if already in wishlist
    $checkQuery = "SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $userId, $productId);
    $checkStmt->execute();
    
    if ($checkStmt->get_result()->num_rows > 0) {
        return false;
    }
    
    // Add to wishlist
    $insertQuery = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("ii", $userId, $productId);
    
    return $insertStmt->execute();
}

/**
 * Remove product from user's wishlist
 * @param int $userId User ID
 * @param int $productId Product ID
 * @return bool True on success, false on failure
 */
function removeFromWishlist($userId, $productId) {
    global $conn;
    
    $query = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $userId, $productId);
    
    return $stmt->execute();
}

/**
 * Get user's wishlist items
 * @param int $userId User ID
 * @return array Array of wishlist items
 */
function getUserWishlist($userId) {
    global $conn;
    
    $query = "SELECT p.* FROM wishlist w 
              JOIN products p ON w.product_id = p.id 
              WHERE w.user_id = ? 
              ORDER BY w.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Create an order from cart items
 * @param int $userId User ID
 * @param array $address Shipping address data
 * @return int|bool Order ID on success, false on failure
 */
function createOrderFromCart($userId, $address) {
    global $conn;
    
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return false;
    }
    
    $conn->begin_transaction();
    
    try {
        // Create order
        $totalAmount = getCartTotal();
        $shippingFee = 10000; // Fixed shipping fee for example
        $orderQuery = "INSERT INTO orders (user_id, total_amount, shipping_fee, status, shipping_address) 
                       VALUES (?, ?, ?, 'pending', ?)";
        $orderStmt = $conn->prepare($orderQuery);
        $orderStmt->bind_param("idss", $userId, $totalAmount, $shippingFee, json_encode($address));
        $orderStmt->execute();
        $orderId = $conn->insert_id;
        
        // Add order items
        foreach ($_SESSION['cart'] as $item) {
            $itemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                          VALUES (?, ?, 1, ?)";
            $itemStmt = $conn->prepare($itemQuery);
            $itemStmt->bind_param("iid", $orderId, $item['id'], $item['price']);
            $itemStmt->execute();
        }
        
        // Clear cart
        unset($_SESSION['cart']);
        
        $conn->commit();
        return $orderId;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

/**
 * Get user's orders
 * @param int $userId User ID
 * @return array Array of orders
 */
function getUserOrders($userId) {
    global $conn;
    
    $query = "SELECT o.id as order_id, o.order_date, o.total_amount, o.status, 
              GROUP_CONCAT(p.title SEPARATOR ', ') as products
              FROM orders o
              JOIN order_items oi ON o.id = oi.order_id
              JOIN products p ON oi.product_id = p.id
              WHERE o.user_id = ?
              GROUP BY o.id
              ORDER BY o.order_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get order details
 * @param int $orderId Order ID
 * @return array|null Order details or null if not found
 */
function getOrderDetails($orderId) {
    global $conn;
    
    // Get order info
    $orderQuery = "SELECT o.*, u.nama, u.email, u.whatsapp 
                   FROM orders o
                   JOIN users u ON o.user_id = u.id
                   WHERE o.id = ?";
    $orderStmt = $conn->prepare($orderQuery);
    $orderStmt->bind_param("i", $orderId);
    $orderStmt->execute();
    $order = $orderStmt->get_result()->fetch_assoc();
    
    if (!$order) {
        return null;
    }
    
    // Get order items
    $itemsQuery = "SELECT p.title, p.image_url, oi.quantity, oi.price 
                   FROM order_items oi 
                   JOIN products p ON oi.product_id = p.id 
                   WHERE oi.order_id = ?";
    $itemsStmt = $conn->prepare($itemsQuery);
    $itemsStmt->bind_param("i", $orderId);
    $itemsStmt->execute();
    $items = $itemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $order['items'] = $items;
    return $order;
}

/**
 * Send email using PHP's mail function (basic implementation)
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $message Email body
 * @param string $from Sender email
 * @return bool True on success, false on failure
 */
function sendEmail($to, $subject, $message, $from = 'no-reply@irabeauty.com') {
    $headers = "From: $from\r\n";
    $headers .= "Reply-To: $from\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Send order confirmation email
 * @param int $orderId Order ID
 * @return bool True on success, false on failure
 */
function sendOrderConfirmationEmail($orderId) {
    $order = getOrderDetails($orderId);
    if (!$order) return false;
    
    $userEmail = $order['email'];
    $subject = "Order Confirmation #" . $order['id'];
    
    ob_start();
    include 'templates/email_order_confirmation.php';
    $message = ob_get_clean();
    
    return sendEmail($userEmail, $subject, $message);
}

/**
 * Get pagination links
 * @param int $totalItems Total number of items
 * @param int $itemsPerPage Items per page
 * @param int $currentPage Current page number
 * @param string $baseUrl Base URL for pagination links
 * @return array Pagination data
 */
function getPagination($totalItems, $itemsPerPage, $currentPage, $baseUrl) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    
    $prevPage = ($currentPage > 1) ? $currentPage - 1 : null;
    $nextPage = ($currentPage < $totalPages) ? $currentPage + 1 : null;
    
    return [
        'total_items' => $totalItems,
        'items_per_page' => $itemsPerPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'prev_page' => $prevPage,
        'next_page' => $nextPage,
        'base_url' => $baseUrl
    ];
}

/**
 * Generate pagination HTML
 * @param array $pagination Pagination data from getPagination()
 * @return string HTML for pagination links
 */
function generatePaginationHtml($pagination) {
    $html = '<div class="pagination">';
    
    // Previous link
    if ($pagination['prev_page']) {
        $html .= '<a href="' . $pagination['base_url'] . '?page=' . $pagination['prev_page'] . '" class="page-link">Previous</a>';
    }
    
    // Page links
    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        $activeClass = ($i == $pagination['current_page']) ? 'active' : '';
        $html .= '<a href="' . $pagination['base_url'] . '?page=' . $i . '" class="page-link ' . $activeClass . '">' . $i . '</a>';
    }
    
    // Next link
    if ($pagination['next_page']) {
        $html .= '<a href="' . $pagination['base_url'] . '?page=' . $pagination['next_page'] . '" class="page-link">Next</a>';
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Get popular products
 * @param int $limit Number of products to return
 * @return array Array of popular products
 */
function getPopularProducts($limit = 5) {
    global $conn;
    
    $query = "SELECT p.*, COUNT(oi.product_id) as order_count 
              FROM products p
              LEFT JOIN order_items oi ON p.id = oi.product_id
              GROUP BY p.id
              ORDER BY order_count DESC, p.id DESC
              LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get new products
 * @param int $limit Number of products to return
 * @return array Array of new products
 */
function getNewProducts($limit = 5) {
    global $conn;
    
    $query = "SELECT * FROM products ORDER BY id DESC LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get products by category
 * @param string $category Category name
 * @param int $limit Number of products to return
 * @return array Array of products in category
 */
function getProductsByCategory($category, $limit = 10) {
    global $conn;
    
    $query = "SELECT * FROM products WHERE category = ? ORDER BY id DESC LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $category, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get related products (same brand)
 * @param int $productId Product ID to find related products for
 * @param int $limit Number of products to return
 * @return array Array of related products
 */
function getRelatedProducts($productId, $limit = 4) {
    global $conn;
    
    // First get the product's brand
    $product = getProductById($productId);
    if (!$product) return [];
    
    $brand = $product['brand'];
    
    $query = "SELECT * FROM products WHERE brand = ? AND id != ? ORDER BY RAND() LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $brand, $productId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Search products
 * @param string $query Search query
 * @param int $limit Number of results to return
 * @return array Array of matching products
 */
function searchProducts($query, $limit = 10) {
    global $conn;
    
    $searchQuery = "%" . $conn->real_escape_string($query) . "%";
    $query = "SELECT * FROM products 
              WHERE title LIKE ? OR brand LIKE ? OR category LIKE ?
              ORDER BY id DESC
              LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $searchQuery, $searchQuery, $searchQuery, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get admin dashboard statistics
 * @return array Dashboard stats
 */
function getDashboardStats() {
    global $conn;
    
    $stats = [];
    
    // Total products
    $products = $conn->query("SELECT COUNT(*) as count FROM products");
    $stats['products'] = $products->fetch_assoc()['count'];
    
    // Total orders
    $orders = $conn->query("SELECT COUNT(*) as count FROM orders");
    $stats['orders'] = $orders->fetch_assoc()['count'];
    
    // Total users
    $users = $conn->query("SELECT COUNT(*) as count FROM users");
    $stats['users'] = $users->fetch_assoc()['count'];
    
    // Total revenue
    $revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'");
    $stats['revenue'] = $revenue->fetch_assoc()['total'] ?? 0;
    
    // Recent orders
    $recentOrders = $conn->query("SELECT o.id, o.order_date, o.total_amount, o.status, u.nama 
                                 FROM orders o 
                                 JOIN users u ON o.user_id = u.id 
                                 ORDER BY o.order_date DESC 
                                 LIMIT 5");
    $stats['recent_orders'] = $recentOrders->fetch_all(MYSQLI_ASSOC);
    
    return $stats;
}