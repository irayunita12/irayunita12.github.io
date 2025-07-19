<?php
require 'auth_check.php';
// Verify admin role
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
require 'db.php';

// Get report data from database
$salesQuery = "SELECT 
                DATE_FORMAT(order_date, '%Y-%m') as month, 
                SUM(total_amount) as total_sales, 
                COUNT(*) as order_count 
               FROM orders 
               WHERE status = 'completed' 
               GROUP BY DATE_FORMAT(order_date, '%Y-%m') 
               ORDER BY month DESC 
               LIMIT 6";
$salesResult = mysqli_query($conn, $salesQuery);
$salesData = mysqli_fetch_all($salesResult, MYSQLI_ASSOC);

// Get top products
$topProductsQuery = "SELECT 
                      p.title, 
                      p.brand, 
                      SUM(oi.quantity) as total_sold, 
                      SUM(oi.price * oi.quantity) as total_revenue 
                     FROM order_items oi 
                     JOIN products p ON oi.product_id = p.id 
                     JOIN orders o ON oi.order_id = o.id 
                     WHERE o.status = 'completed' 
                     GROUP BY p.id 
                     ORDER BY total_sold DESC 
                     LIMIT 5";
$topProductsResult = mysqli_query($conn, $topProductsQuery);
$topProducts = mysqli_fetch_all($topProductsResult, MYSQLI_ASSOC);

// Get customer stats
$customerStatsQuery = "SELECT 
                        COUNT(*) as total_customers,
                        (SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as new_customers,
                        (SELECT COUNT(*) FROM orders GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT 1) as max_orders
                       FROM users";
$customerStatsResult = mysqli_query($conn, $customerStatsQuery);
$customerStats = mysqli_fetch_assoc($customerStatsResult);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Ira Skincare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    .report-section {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        margin-bottom: 20px;
        color: #343a40;
        font-size: 1.3rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .chart-container {
        height: 400px;
        margin-bottom: 20px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
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

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
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

    .badge-primary {
        background-color: #007bff;
        color: white;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .report-actions {
        margin-top: 20px;
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 8px 15px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
        cursor: pointer;
        border: none;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0069d9;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .date-filter {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        align-items: center;
    }

    .date-filter input {
        padding: 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    .date-filter button {
        padding: 8px 15px;
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
                <div class="menu-item active">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
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
                <h1>Sales Reports & Analytics</h1>
                <div>
                    <span><?= date('l, F j, Y') ?></span>
                </div>
            </div>

            <!-- Sales Overview -->
            <div class="report-section">
                <h2 class="section-title">Sales Overview</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Revenue</h3>
                        <p>Rp<?= number_format($customerStats['total_revenue'] ?? 0, 0, ',', '.') ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Orders</h3>
                        <p><?= $customerStats['order_count'] ?? 0 ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Customers</h3>
                        <p><?= $customerStats['total_customers'] ?? 0 ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>New Customers (30 days)</h3>
                        <p><?= $customerStats['new_customers'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <!-- Monthly Sales Chart -->
            <div class="report-section">
                <h2 class="section-title">Monthly Sales</h2>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Top Products -->
            <div class="report-section">
                <h2 class="section-title">Top Selling Products</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Brand</th>
                            <th>Units Sold</th>
                            <th>Total Revenue</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topProducts as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['title']) ?></td>
                            <td><?= htmlspecialchars($product['brand']) ?></td>
                            <td><?= $product['total_sold'] ?></td>
                            <td>Rp<?= number_format($product['total_revenue'], 0, ',', '.') ?></td>
                            <td>
                                <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn btn-primary btn-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Export Options -->
            <div class="report-section">
                <h2 class="section-title">Export Reports</h2>
                <div class="report-actions">
                    <button class="btn btn-primary">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                    <button class="btn btn-success">
                        <i class="fas fa-file-pdf"></i> Export to PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Monthly Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: [
                <?php foreach(array_reverse($salesData) as $data): ?> "<?= date('M Y', strtotime($data['month'] . '-01')) ?>",
                <?php endforeach; ?>
            ],
            datasets: [{
                label: 'Total Sales',
                data: [<?php foreach(array_reverse($salesData) as $data): ?><?= $data['total_sales'] ?>,
                    <?php endforeach; ?>
                ],
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Number of Orders',
                data: [<?php foreach(array_reverse($salesData) as $data): ?><?= $data['order_count'] ?>,
                    <?php endforeach; ?>
                ],
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                type: 'line',
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Sales (Rp)'
                    }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Orders'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
    </script>
</body>

</html>