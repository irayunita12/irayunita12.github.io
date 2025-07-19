<?php
require 'auth_check.php';
require 'db.php';

// Get cart items from localStorage (handled by JavaScript)
$cartItems = [];
$totalPrice = 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Ira Skincare</title>
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

    .checkout-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 5%;
    }

    .checkout-title {
        font-size: 1.8rem;
        margin-bottom: 2rem;
        color: #333;
    }

    .checkout-title span {
        color: #6b9dff;
    }

    .checkout-content {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
    }

    .checkout-items {
        flex: 2;
        min-width: 300px;
    }

    .checkout-summary {
        flex: 1;
        min-width: 300px;
    }

    .checkout-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .checkout-item {
        display: flex;
        gap: 1.5rem;
        padding: 1rem 0;
        border-bottom: 1px solid #eee;
    }

    .checkout-item:last-child {
        border-bottom: none;
    }

    .checkout-item-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
    }

    .checkout-item-details {
        flex: 1;
    }

    .checkout-item-title {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .checkout-item-brand {
        font-size: 0.9rem;
        color: #777;
        margin-bottom: 0.5rem;
    }

    .checkout-item-price {
        font-weight: 700;
        color: #6b9dff;
        font-size: 1.1rem;
    }

    .checkout-item-quantity {
        font-size: 0.9rem;
        color: #555;
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
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .checkout-btn:hover {
        background-color: #5a89e0;
    }

    .back-to-cart {
        display: inline-block;
        margin-top: 1.5rem;
        color: #6b9dff;
        text-decoration: none;
        font-weight: 500;
    }

    .back-to-cart:hover {
        text-decoration: underline;
    }

    .customer-info {
        margin-bottom: 2rem;
    }

    .info-title {
        font-size: 1.2rem;
        margin-bottom: 1rem;
        color: #333;
    }

    .info-detail {
        margin-bottom: 0.5rem;
        color: #555;
    }

    .info-detail strong {
        color: #333;
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

        .checkout-item {
            flex-direction: column;
            gap: 1rem;
        }

        .checkout-item-img {
            width: 100%;
            height: auto;
        }
    }

    .notification {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        background-color: #4bb543;
        color: white;
        padding: 12px 20px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        opacity: 0;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .notification.show {
        transform: translateX(-50%) translateY(0);
        opacity: 1;
    }

    .notification i {
        font-size: 1.2rem;
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

    <div class="checkout-container">
        <h1 class="checkout-title">Checkout <span>Pesanan</span></h1>

        <div class="checkout-content">
            <div class="checkout-items">
                <div class="customer-info checkout-card">
                    <h3 class="info-title">Informasi Pelanggan</h3>
                    <p class="info-detail"><strong>Nama:</strong> <?= htmlspecialchars($_SESSION['nama']) ?></p>
                    <p class="info-detail"><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email']) ?></p>
                    <?php 
                    // Get additional user info from database
                    $userId = $_SESSION['user_id'];
                    $userQuery = mysqli_query($conn, "SELECT whatsapp FROM users WHERE id = '$userId'");
                    $userData = mysqli_fetch_assoc($userQuery);
                    ?>
                    <p class="info-detail"><strong>WhatsApp:</strong>
                        <?= htmlspecialchars($userData['whatsapp'] ?? 'Belum diisi') ?></p>
                    <a href="profile.php" class="back-to-cart">
                        <i class="fas fa-edit"></i> Edit Profil
                    </a>
                </div>

                <div class="checkout-card">
                    <h3 class="summary-title">Produk Anda</h3>
                    <div id="checkoutItems">
                        <!-- Items will be loaded by JavaScript -->
                        <p class="no-items">Keranjang Anda kosong</p>
                    </div>
                </div>
            </div>

            <div class="checkout-summary">
                <div class="checkout-card">
                    <h3 class="summary-title">Ringkasan Belanja</h3>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">Rp0</span>
                    </div>
                    <div class="summary-row">
                        <span>Ongkos Kirim</span>
                        <span>Konfirmasi Admin</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span id="total">Rp0</span>
                    </div>
                    <button class="checkout-btn" onclick="completeCheckout()">
                        <i class="fas fa-whatsapp"></i> Konfirmasi via WhatsApp
                    </button>
                    <a href="cart.php" class="back-to-cart">
                        <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Load cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let subtotal = 0;

    // Function to display checkout items
    function displayCheckoutItems() {
        const checkoutItems = document.getElementById('checkoutItems');
        const subtotalElement = document.getElementById('subtotal');
        const totalElement = document.getElementById('total');

        if (cart.length === 0) {
            checkoutItems.innerHTML = '<p class="no-items">Keranjang Anda kosong</p>';
            subtotalElement.textContent = 'Rp0';
            totalElement.textContent = 'Rp0';
            return;
        }

        // Calculate subtotal
        subtotal = 0;
        const itemCount = {};

        // Count quantity of each item
        cart.forEach(item => {
            const key = `${item.id}-${item.title}`;
            itemCount[key] = (itemCount[key] || 0) + 1;
            subtotal += parseInt(item.price);
        });

        // Generate checkout items HTML
        let itemsHTML = '';
        Object.keys(itemCount).forEach(key => {
            const [id, title] = key.split('-').slice(0, 2);
            const item = cart.find(i => i.id == id && i.title == title);
            const quantity = itemCount[key];

            itemsHTML += `
                <div class="checkout-item">
                    <img src="${item.image_url}" alt="${item.title}" class="checkout-item-img">
                    <div class="checkout-item-details">
                        <h4 class="checkout-item-title">${item.title}</h4>
                        <p class="checkout-item-brand">${item.brand || 'Ira Skincare'}</p>
                        <p class="checkout-item-price">Rp${parseInt(item.price).toLocaleString('id-ID')}</p>
                        <p class="checkout-item-quantity">Jumlah: ${quantity}</p>
                    </div>
                </div>
            `;
        });

        checkoutItems.innerHTML = itemsHTML;
        subtotalElement.textContent = `Rp${subtotal.toLocaleString('id-ID')}`;
        totalElement.textContent = `Rp${subtotal.toLocaleString('id-ID')}`;
    }

    // Function to complete checkout
    function completeCheckout() {
        if (cart.length === 0) {
            showNotification('Keranjang belanja kosong!');
            return;
        }

        const adminData = {
            whatsapp: "+6285651378535",
            name: "Admin Ira Skincare"
        };

        // Get user info from PHP session
        const userInfo = {
            name: "<?= htmlspecialchars($_SESSION['nama']) ?>",
            whatsapp: "<?= htmlspecialchars($userData['whatsapp'] ?? '') ?>"
        };

        let message = `Halo ${adminData.name}, saya ingin memesan:\n\n`;
        let totalHarga = 0;
        const itemCount = {};

        // Count quantity of each item
        cart.forEach(item => {
            const key = `${item.id}-${item.title}`;
            itemCount[key] = (itemCount[key] || 0) + 1;
        });

        // Add items to message
        Object.keys(itemCount).forEach(key => {
            const [id, title] = key.split('-').slice(0, 2);
            const item = cart.find(i => i.id == id && i.title == title);
            const quantity = itemCount[key];

            message +=
                `- ${title} (${quantity}x) - Rp${(parseInt(item.price) * quantity).toLocaleString('id-ID')}\n`;
            totalHarga += parseInt(item.price) * quantity;
        });

        message += `\n*Total Items:* ${cart.length}\n`;
        message += `*Subtotal:* Rp${totalHarga.toLocaleString('id-ID')}\n`;
        message += `*Ongkir:* (Konfirmasi admin)\n`;
        message += `*Total Pembayaran:* Rp${totalHarga.toLocaleString('id-ID')}\n\n`;
        message += `*Data Pelanggan:*\n`;
        message += `Nama: ${userInfo.name}\n`;
        message += `WhatsApp: ${userInfo.whatsapp || 'Belum diisi'}\n\n`;
        message += `Silakan konfirmasi ketersediaan barang dan total pembayaran. Terima kasih!`;

        const encodedMessage = encodeURIComponent(message);
        const whatsappUrl = `https://wa.me/${adminData.whatsapp}?text=${encodedMessage}`;

        // Clear cart after checkout
        localStorage.removeItem('cart');
        cart = [];

        // Redirect to WhatsApp
        window.open(whatsappUrl, '_blank');

        // Redirect to thank you page after a delay
        setTimeout(() => {
            window.location.href = 'thankyou.php';
        }, 1000);
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

    // Initialize checkout display
    displayCheckoutItems();
    </script>
</body>

</html>