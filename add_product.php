<?php
// add_product.php
require 'auth_check.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];
    $skin_type = json_encode(explode(',', $_POST['skin_type']));

    $query = "INSERT INTO products (title, brand, price, image_url, skin_type) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssdss', $title, $brand, $price, $image_url, $skin_type);
    mysqli_stmt_execute($stmt);

    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        margin: 0;
        padding: 20px;
        background-color: #f5f5f5;
    }

    .container {
        max-width: 600px;
        margin: 0 auto;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        color: #333;
    }

    .product-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .form-group label {
        font-weight: bold;
    }

    .form-group input {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }

    .submit-btn {
        background-color: #4c81afff;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 10px;
    }

    .submit-btn:hover {
        background-color: #1f82c4ff;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Tambah Produk Baru</h1>
        <form method="POST" action="add_product.php" class="product-form">
            <div class="form-group">
                <label for="title">Nama Produk</label>
                <input type="text" id="title" name="title" placeholder="Masukkan nama produk" required>
            </div>
            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" id="brand" name="brand" placeholder="Masukkan nama brand" required>
            </div>
            <div class="form-group">
                <label for="price">Harga</label>
                <input type="number" id="price" name="price" placeholder="Masukkan harga" required>
            </div>
            <div class="form-group">
                <label for="image_url">URL Gambar</label>
                <input type="text" id="image_url" name="image_url" placeholder="Masukkan URL gambar produk" required>
            </div>
            <div class="form-group">
                <label for="skin_type">Jenis Kulit (pisahkan dengan koma)</label>
                <input type="text" id="skin_type" name="skin_type" placeholder="Contoh: kering,berminyak,sensitif"
                    required>
            </div>
            <button type="submit" class="submit-btn">Tambah Produk</button>
        </form>
    </div>
</body>

</html>