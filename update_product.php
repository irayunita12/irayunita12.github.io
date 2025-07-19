<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_products.php");
    exit;
}

// Validasi input
$id = intval($_POST['id']);
$title = trim($_POST['title']);
$brand = trim($_POST['brand']);
$category = trim($_POST['category'] ?? '');
$price = floatval($_POST['price']);
$image_url = trim($_POST['image_url'] ?? '');

// Validasi URL gambar
if (!empty($image_url)) {
    if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
        die("URL gambar tidak valid");
    }
    
    // Optional: Validasi ekstensi gambar
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $url_parts = parse_url($image_url);
    $path = $url_parts['path'] ?? '';
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowed_extensions)) {
        die("Hanya file gambar (JPG, PNG, GIF, WEBP) yang diperbolehkan");
    }
}

// Update database
$query = "UPDATE products SET 
          title = ?,
          brand = ?,
          category = ?,
          price = ?,
          image_url = ?
          WHERE id = ?";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("sssssi", $title, $brand, $category, $price, $image_url, $id);

if ($stmt->execute()) {
    header("Location: admin_products.php?success=1");
} else {
    die("Error updating product: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>