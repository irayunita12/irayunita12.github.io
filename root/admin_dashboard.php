<?php
require 'auth_check.php';
// Verify admin role
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
require 'db.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ira Skincare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .stat-card h3 {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .stat-card p {
        font-size: 24px;
        font-weight: bold;
        color: #343a40;
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
                <div class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </div>
                <div class="menu-item" onclick="window.location.href='admin_products.php'">
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
                <h1>Dashboard Overview</h1>
                <div>
                    <span><?= date('l, F j, Y') ?></span>
                </div>
            </div>

            <div class="stats-container">
                <?php
                // Get stats from database
                $products = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
                $products_count = mysqli_fetch_assoc($products)['count'];
                
                $orders = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders");
                $orders_count = mysqli_fetch_assoc($orders)['count'];
                
                $users = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
                $users_count = mysqli_fetch_assoc($users)['count'];
                
                $revenue = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'");
                $revenue_total = mysqli_fetch_assoc($revenue)['total'] ?? 0;
                ?>

                <div class="stat-card">
                    <h3>Total Products</h3>
                    <p><?= $products_count ?></p>
                </div>

                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <p><?= $orders_count ?></p>
                </div>

                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p><?= $users_count ?></p>
                </div>

                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <p>Rp<?= number_format($revenue_total, 0, ',', '.') ?></p>
                </div>
            </div>

            <!-- Recent Orders Section -->
            <div class="recent-orders">
                <h2>Recent Orders</h2>
                <?php
                $recent_orders = mysqli_query($conn, "SELECT o.id, o.order_date, o.total_amount, o.status, u.nama 
                                                     FROM orders o 
                                                     JOIN users u ON o.user_id = u.id 
                                                     ORDER BY o.order_date DESC 
                                                     LIMIT 5");
                
                if (mysqli_num_rows($recent_orders) > 0) {
                    echo '<table class="table">';
                    echo '<thead><tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr></thead>';
                    echo '<tbody>';
                    
                    while ($order = mysqli_fetch_assoc($recent_orders)) {
                        echo '<tr>
                                <td>#' . $order['id'] . '</td>
                                <td>' . htmlspecialchars($order['nama']) . '</td>
                                <td>' . date('M d, Y', strtotime($order['order_date'])) . '</td>
                                <td>Rp' . number_format($order['total_amount'], 0, ',', '.') . '</td>
                                <td><span class="badge">' . ucfirst($order['status']) . '</span></td>
                                <td><a href="order_detail.php?id=' . $order['id'] . '">View</a></td>
                              </tr>';
                    }
                    
                    echo '</tbody></table>';
                } else {
                    echo '<p>No recent orders found.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>