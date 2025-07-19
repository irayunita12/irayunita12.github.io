<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: admin_products.php");
    exit;
}

$product_id = intval($_GET['id']);
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Produk tidak ditemukan");
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Product</title>
    <style>
    * {
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #f5f5f5;
        margin: 0;
        padding: 20px;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        background-color: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        color: #333;
        margin-top: 0;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #555;
    }

    input[type="text"],
    input[type="number"],
    input[type="url"] {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        transition: border-color 0.3s;
    }

    input:focus {
        border-color: #4a90e2;
        outline: none;
        box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
    }

    button {
        background-color: #4a90e2;
        color: white;
        border: none;
        padding: 12px 20px;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #3a7bc8;
    }

    .back-link {
        display: inline-block;
        margin-top: 20px;
        color: #4a90e2;
        text-decoration: none;
        font-size: 14px;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .image-preview {
        max-width: 100%;
        max-height: 300px;
        margin: 15px 0;
        border: 1px solid #eee;
        padding: 5px;
        border-radius: 4px;
    }

    .current-image {
        margin-top: 10px;
    }

    .url-status {
        font-size: 14px;
        margin-top: 5px;
    }

    .valid {
        color: green;
    }

    .invalid {
        color: red;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Edit Product: <?php echo htmlspecialchars($product['title']); ?></h1>

        <form action="update_product.php" method="post">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>
            </div>

            <div class="form-group">
                <label>Brand</label>
                <input type="text" name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>" required>
            </div>

            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" value="<?php echo htmlspecialchars($product['category'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Price</label>
                <input type="number" name="price" value="<?php echo $product['price']; ?>" min="0" step="0.01" required>
            </div>

            <div class="form-group">
                <label>Image URL</label>
                <input type="url" name="image_url" id="image-url"
                    value="<?php echo htmlspecialchars($product['image_url'] ?? ''); ?>"
                    placeholder="https://example.com/image.jpg">
                <div class="url-status" id="url-status"></div>

                <?php if (!empty($product['image_url'])): ?>
                <div class="current-image">
                    <p>Current Image:</p>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="image-preview"
                        id="image-preview">
                </div>
                <?php endif; ?>
            </div>

            <button type="submit">Update Product</button>
            <a href="admin_products.php" class="back-link">← Back to Products</a>
        </form>
    </div>

    <script>
    // Live URL validation and preview
    document.getElementById('image-url').addEventListener('input', function() {
        const url = this.value;
        const urlStatus = document.getElementById('url-status');
        const imagePreview = document.getElementById('image-preview');

        if (url) {
            try {
                new URL(url); // Basic URL validation

                // Update preview
                if (imagePreview) {
                    imagePreview.src = url;
                } else {
                    const newPreview = document.createElement('img');
                    newPreview.src = url;
                    newPreview.className = 'image-preview';
                    newPreview.id = 'image-preview';
                    this.parentNode.appendChild(newPreview);
                }

                urlStatus.textContent = 'URL valid';
                urlStatus.className = 'url-status valid';
            } catch (e) {
                urlStatus.textContent = 'URL tidak valid';
                urlStatus.className = 'url-status invalid';
            }
        } else {
            urlStatus.textContent = '';
            if (imagePreview) imagePreview.style.display = 'none';
        }
    });
    </script>
</body>

</html>