<?php
require 'auth_check.php';
require 'db.php';

// Get cart items from localStorage (will be handled by JavaScript)
// In a real application, you might want to store cart in database for logged-in users
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Ira Skincare</title>
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

    .cart-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 5%;
    }

    .cart-title {
        font-size: 1.8rem;
        margin-bottom: 2rem;
        color: #333;
    }

    .cart-title span {
        color: #6b9dff;
    }

    .cart-empty {
        text-align: center;
        padding: 3rem;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    }

    .cart-empty i {
        font-size: 3rem;
        color: #ddd;
        margin-bottom: 1rem;
    }

    .cart-empty p {
        color: #777;
        margin-bottom: 1.5rem;
    }

    .cart-empty .btn {
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

    .cart-empty .btn:hover {
        background-color: #5a89e0;
    }

    .cart-content {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
    }

    .cart-items {
        flex: 2;
        min-width: 300px;
    }

    .cart-summary {
        flex: 1;
        min-width: 300px;
    }

    .cart-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .cart-item {
        display: flex;
        gap: 1.5rem;
        padding: 1rem 0;
        border-bottom: 1px solid #eee;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .cart-item-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
    }

    .cart-item-details {
        flex: 1;
    }

    .cart-item-title {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .cart-item-brand {
        font-size: 0.9rem;
        color: #777;
        margin-bottom: 0.5rem;
    }

    .cart-item-price {
        font-weight: 700;
        color: #6b9dff;
        font-size: 1.1rem;
    }

    .cart-item-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 0.5rem;
    }

    .quantity-btn {
        width: 30px;
        height: 30px;
        border: 1px solid #ddd;
        background: none;
        font-size: 1rem;
        cursor: pointer;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quantity-input {
        width: 50px;
        height: 30px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .remove-item {
        color: #ff6b6b;
        cursor: pointer;
        font-size: 0.9rem;
        margin-left: auto;
    }

    .summary-title {
        font-size: 1.3rem;
        margin-bottom: 1.5rem;
        color: #333;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }

    .summary-total {
        font-weight: 700;
        font-size: 1.2rem;
    }

    .checkout-btn {
        width: 100%;
        padding: 1rem;
        background-color: #6b9dff;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 1.5rem;
        font-size: 1rem;
    }

    .checkout-btn:hover {
        background-color: #5a89e0;
    }

    .continue-shopping {
        display: inline-block;
        margin-top: 1.5rem;
        color: #6b9dff;
        text-decoration: none;
        font-weight: 500;
    }

    .continue-shopping:hover {
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

        .cart-item {
            flex-direction: column;
            gap: 1rem;
        }

        .cart-item-img {
            width: 100%;
            height: auto;
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

    <div class="cart-container">
        <h1 class="cart-title">Keranjang <span>Belanja</span></h1>

        <div id="cartContent">
            <!-- Cart content will be loaded by JavaScript -->
            <div class="cart-empty">
                <i class="fas fa-shopping-cart"></i>
                <h3>Keranjang Anda kosong</h3>
                <p>Mulai belanja produk skincare favorit Anda sekarang</p>
                <a href="products.php" class="btn">Belanja Sekarang</a>
            </div>
        </div>
    </div>

    <script>
    // Load cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Function to display cart items
    function displayCart() {
        const cartContent = document.getElementById('cartContent');

        if (cart.length === 0) {
            cartContent.innerHTML = `
                <div class="cart-empty">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Keranjang Anda kosong</h3>
                    <p>Mulai belanja produk skincare favorit Anda sekarang</p>
                    <a href="products.php" class="btn">Belanja Sekarang</a>
                </div>
            `;
            return;
        }

        // Calculate total price
        let subtotal = 0;
        cart.forEach(item => {
            subtotal += parseInt(item.price);
        });

        // Generate cart HTML
        let cartHTML = `
            <div class="cart-content">
                <div class="cart-items">
                    <div class="cart-card">
                        <h3 class="summary-title">Produk Anda (${cart.length})</h3>
                        ${cart.map((item, index) => `
                            <div class="cart-item" data-id="${item.id}">
                                <img src="${item.image_url}" alt="${item.title}" class="cart-item-img">
                                <div class="cart-item-details">
                                    <h4 class="cart-item-title">${item.title}</h4>
                                    <p class="cart-item-brand">${item.brand || 'Ira Skincare'}</p>
                                    <p class="cart-item-price">Rp${parseInt(item.price).toLocaleString('id-ID')}</p>
                                    <div class="cart-item-actions">
                                        <button class="quantity-btn" onclick="changeQuantity(${index}, -1)">-</button>
                                        <input type="number" class="quantity-input" value="1" min="1" id="quantity-${index}">
                                        <button class="quantity-btn" onclick="changeQuantity(${index}, 1)">+</button>
                                        <span class="remove-item" onclick="removeFromCart(${index})">
                                            <i class="fas fa-trash"></i> Hapus
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                <div class="cart-summary">
                    <div class="cart-card">
                        <h3 class="summary-title">Ringkasan Belanja</h3>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>Rp${subtotal.toLocaleString('id-ID')}</span>
                        </div>
                        <div class="summary-row">
                            <span>Ongkos Kirim</span>
                            <span>Konfirmasi Admin</span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>Total</span>
                            <span>Rp${subtotal.toLocaleString('id-ID')}</span>
                        </div>
                        <button class="checkout-btn" onclick="checkout()">
                            <i class="fas fa-whatsapp"></i> Checkout via WhatsApp
                        </button>
                        <a href="products.php" class="continue-shopping">
                            <i class="fas fa-arrow-left"></i> Lanjutkan Belanja
                        </a>
                    </div>
                </div>
            </div>
        `;

        cartContent.innerHTML = cartHTML;
    }

    // Function to change quantity
    function changeQuantity(index, change) {
        const quantityInput = document.getElementById(`quantity-${index}`);
        let newValue = parseInt(quantityInput.value) + change;

        if (newValue < 1) newValue = 1;
        quantityInput.value = newValue;

        // Update cart (for multiple same items)
        // In a real app, you might want to update the cart array
    }

    // Function to remove item from cart
    function removeFromCart(index) {
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        displayCart();
        showNotification('Produk telah dihapus dari keranjang');
    }

    // Function to checkout
    function checkout() {
        if (cart.length === 0) {
            alert('Keranjang belanja kosong!');
            return;
        }

        const adminData = {
            whatsapp: "+6285651378535",
            name: "Admin Ira Skincare"
        };

        let message = `Halo ${adminData.name}, saya ingin memesan:\n\n`;
        let totalHarga = 0;

        cart.forEach((item) => {
            const quantity = document.getElementById(`quantity-${cart.indexOf(item)}`)?.value || 1;
            message +=
                `- ${item.title} (${quantity}x) - Rp${(parseInt(item.price) * quantity).toLocaleString('id-ID')}\n`;
            totalHarga += parseInt(item.price) * quantity;
        });

        message += `\n*Total Items:* ${cart.length}\n`;
        message += `*Subtotal:* Rp${totalHarga.toLocaleString('id-ID')}\n`;
        message += `*Ongkir:* (Konfirmasi admin)\n`;
        message += `*Total Pembayaran:* Rp${totalHarga.toLocaleString('id-ID')}\n\n`;
        message += `Silakan konfirmasi ketersediaan barang. Terima kasih!`;

        const encodedMessage = encodeURIComponent(message);
        const whatsappUrl = `https://wa.me/${adminData.whatsapp}?text=${encodedMessage}`;

        window.open(whatsappUrl, '_blank');
    }

    // Function to show notification
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Initialize cart display
    displayCart();
    </script>
</body>

</html>