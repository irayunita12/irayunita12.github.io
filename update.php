<?php
require_once 'config.php';

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_products.php");
    exit;
}

// Validasi dan sanitasi input
$id = intval($_POST['id']);
$title = trim($_POST['title']);
$brand = trim($_POST['brand']);
$category = trim($_POST['category'] ?? ''); // Gunakan empty string jika tidak ada
$price = floatval($_POST['price']);
$stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0; // Default 0 jika tidak ada

// Gunakan prepared statement untuk keamanan
$query = "UPDATE products SET 
          title = ?, 
          brand = ?, 
          category = ?, 
          price = ?, 
          stock = ? 
          WHERE id = ?";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("sssdii", $title, $brand, $category, $price, $stock, $id);

if ($stmt->execute()) {
    header("Location: admin_products.php?success=1");
} else {
    // Tampilkan error detail
    die("Error updating product: " . $stmt->error);
}

$stmt->close();
$conn->close();
exit;