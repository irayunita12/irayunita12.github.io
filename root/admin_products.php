<?php
require 'auth_check.php';
// Verify admin role
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
require 'db.php';

// Handle product deletion
if (isset($_GET['delete'])) {
    $productId = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    if ($stmt->execute()) {
        $success = "Product deleted successfully";
    } else {
        $error = "Failed to delete product: " . $stmt->error;
    }
}

// Get all products from database
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
$productCount = mysqli_num_rows($products);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Ira Skincare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #f8f9fa;
    }

    .admin-container {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 250px;
        background-color: #343a40;
        color: white;
        padding: 20px 0;
    }

    .sidebar-header {
        padding: 0 20px 20px;
        border-bottom: 1px solid #4b545c;
    }

    .sidebar-menu {
        margin-top: 20px;
    }

    .menu-item {
        padding: 12px 20px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .menu-item:hover {
        background-color: #495057;
    }

    .menu-item.active {
        background-color: #007bff;
    }

    .menu-item i {
        margin-right: 10px;
    }

    .main-content {
        flex: 1;
        padding: 20px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #dee2e6;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
    }

    .btn {
        padding: 8px 15px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
        border: 1px solid #007bff;
    }

    .btn-primary:hover {
        background-color: #0069d9;
        border-color: #0062cc;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
        border: 1px solid #dc3545;
    }

    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
    }

    .table th,
    .table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .table tr:hover {
        background-color: #f8f9fa;
    }

    .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
    }

    .badge {
        display: inline-block;
        padding: 3px 7px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        border-radius: 10px;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
    }

    .search-container {
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
    }

    .search-input {
        flex: 1;
        padding: 8px 15px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    .search-btn {
        padding: 8px 15px;
        background-color: #6c757d;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        gap: 5px;
    }

    .page-item {
        list-style: none;
    }

    .page-link {
        display: block;
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        color: #007bff;
        text-decoration: none;
    }

    .page-link:hover {
        background-color: #e9ecef;
    }

    .page-item.active .page-link {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    </style>
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Welcome, <?= htmlspecialchars($_SESSION['nama']) ?></p>
            </div>

            <div class="sidebar-menu">
                <div class="menu-item" onclick="window.location.href='admin_dashboard.php'">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </div>
                <div class="menu-item active">
                    <i class="fas fa-box-open"></i>
                    <span>Products</span>
                </div>
                <div class="menu-item" onclick="window.location.href='admin_orders.php'">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </div>
                <div class="menu-item" onclick="window.location.href='admin_users.php'">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </div>
                <div class="menu-item" onclick="window.location.href='logout.php'">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Manage Products</h1>
                <a href="add_product.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Product
                </a>
            </div>

            <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?= $success ?>
            </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?= $error ?>
            </div>
            <?php endif; ?>

            <div class="search-container">
                <input type="text" class="search-input" placeholder="Search products...">
                <button class="search-btn">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = mysqli_fetch_assoc($products)): ?>
                        <tr>
                            <td><?= $product['id'] ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($product['image_url']) ?>" class="product-image"
                                    alt="<?= htmlspecialchars($product['title']) ?>">
                            </td>
                            <td><?= htmlspecialchars($product['title']) ?></td>
                            <td><?= htmlspecialchars($product['brand']) ?></td>
                            <td><?= htmlspecialchars($product['category']) ?></td>
                            <td>Rp<?= number_format($product['price'], 0, ',', '.') ?></td>
                            <td>
                                <?php 
                                // Assuming we have a stock field in the database
                                $stock = $product['stock'] ?? 0;
                                if ($stock > 10) {
                                    echo '<span class="badge badge-success">In Stock</span>';
                                } elseif ($stock > 0) {
                                    echo '<span class="badge badge-warning">Low Stock</span>';
                                } else {
                                    echo '<span class="badge badge-danger">Out of Stock</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="admin_products.php?delete=<?= $product['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this product?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($productCount == 0): ?>
            <p>No products found. <a href="add_product.php">Add your first product</a></p>
            <?php endif; ?>

            <div class="pagination">
                <ul class="page-item">
                    <a href="#" class="page-link">Previous</a>
                </ul>
                <ul class="page-item active">
                    <a href="#" class="page-link">1</a>
                </ul>
                <ul class="page-item">
                    <a href="#" class="page-link">2</a>
                </ul>
                <ul class="page-item">
                    <a href="#" class="page-link">3</a>
                </ul>
                <ul class="page-item">
                    <a href="#" class="page-link">Next</a>
                </ul>
            </div>
        </div>
    </div>
</body>

</html>