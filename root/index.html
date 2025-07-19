<?php
require 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ira Beauty - Toko Skincare Online Terpercaya</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Header Section -->
    <header class="header">
        <div class="container">
            <div class="logo-container">
                <img src="logo.png" alt="Ira Beauty Logo" class="logo-img">
                <div class="logo-text">Ira<span>Beauty</span></div>
            </div>

            <div class="search-container">
                <form action="search.php" method="GET">
                    <input type="text" class="search-bar" placeholder="Cari produk skincare..." name="q">
                    </button>
                </form>
            </div>

            <div class="menu-container">
                <div class="cart-icon" id="cartIcon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount">0</span>
                </div>

                <?php if(isset($_SESSION['user_id'])): ?>
                <div class="user-dropdown">
                    <button class="auth-button">
                        <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['nama']) ?>
                    </button>
                    <div class="dropdown-content">
                        <a href="profile.php"><i class="fas fa-user-circle"></i> Profil</a>
                        <a href="order_history.php"><i class="fas fa-history"></i> Riwayat Belanja</a>
                        <a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
                <?php else: ?>
                <div class="auth-buttons">
                    <a href="login.php" class="auth-button">Login</a>
                    <a href="register.php" class="auth-button secondary">Daftar</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="main-nav">
        <div class="container">
            <ul class="nav-links">
                <li><a href="index.php" class="active">Beranda</a></li>
                <li class="dropdown">
                    <a href="products.php">Produk <i class="fas fa-chevron-down"></i></a>
                    <div class="dropdown-content">
                        <a href="products.php?category=cleanser">Pembersih Wajah</a>
                        <a href="products.php?category=toner">Toner</a>
                        <a href="products.php?category=serum">Serum</a>
                        <a href="products.php?category=moisturizer">Pelembap</a>
                    </div>
                </li>
                <li><a href="about.php">Tentang Kami</a></li>
                <li><a href="contact.php">Kontak</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Banner -->
    <section class="hero" style="
    background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.5)), 
                url('https://miro.medium.com/v2/resize:fit:1200/1*8t2bSjNLbYJgNY0ht-LFYg.jpeg');
    background-size: cover;
    background-position: center;
    min-height: 80vh;
    display: flex;
    align-items: center;
    color: white;
">
        <div class="container">
            <div class="hero-content" style="max-width: 600px;">
                <h1 style="font-size: 3rem; margin-bottom: 1rem;">Kulit Sehat, Percaya Diri Maksimal</h1>
                <p style="font-size: 1.2rem; margin-bottom: 2rem;">
                    Temukan rangkaian skincare terbaik dengan bahan alami untuk semua jenis kulit
                </p>
                <a href="products.php" class="btn btn-primary" style="
                background: #ff6b9e;
                border: none;
                padding: 12px 30px;
                font-size: 1.1rem;
                border-radius: 30px;
            ">Belanja Sekarang</a>
            </div>
        </div>
    </section>


    <section class="featured-products">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Produk Unggulan</h2>
                <a href="products.php" class="view-all">Lihat Semua</a>
            </div>

            <div class="products-grid">
                <?php
                require 'db.php';
                $query = "SELECT * FROM products WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 8";
                $result = mysqli_query($conn, $query);
                
                if(mysqli_num_rows($result) > 0):
                    while($product = mysqli_fetch_assoc($result)):
                ?>
                <div class="product-card">
                    <div class="product-badge">
                        <?php if($product['discount'] > 0): ?>
                        <span class="discount-badge">-<?= $product['discount'] ?>%</span>
                        <?php endif; ?>
                        <span class="new-badge">Baru</span>
                    </div>
                    <a href="product_detail.php?id=<?= $product['id'] ?>">
                        <img src="uploads/products/<?= htmlspecialchars($product['image_url']) ?>"
                            alt="<?= htmlspecialchars($product['title']) ?>" class="product-image">
                    </a>
                    <div class="product-info">
                        <span class="product-brand"><?= htmlspecialchars($product['brand']) ?></span>
                        <h3 class="product-title">
                            <a
                                href="product_detail.php?id=<?= $product['id'] ?>"><?= htmlspecialchars($product['title']) ?></a>
                        </h3>
                        <div class="product-price">
                            <?php if($product['discount'] > 0): ?>
                            <span class="original-price">Rp<?= number_format($product['price'], 0, ',', '.') ?></span>
                            <span
                                class="current-price">Rp<?= number_format($product['price'] * (100 - $product['discount']) / 100, 0, ',', '.') ?></span>
                            <?php else: ?>
                            <span class="current-price">Rp<?= number_format($product['price'], 0, ',', '.') ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <button class="btn-wishlist" data-product-id="<?= $product['id'] ?>">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="btn-add-to-cart" data-product-id="<?= $product['id'] ?>">
                                <i class="fas fa-shopping-cart"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <?php
                    endwhile;
                else:
                ?>
                <p class="no-products">Tidak ada produk unggulan saat ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Promo Banner -->
    <section class="promo-banner">
        <div class="container">
            <div class="banner-content">
                <h2>Diskon Spesial 30%</h2>
                <p>Untuk semua produk serum selama bulan ini</p>
                <a href="products.php?category=serum" class="btn btn-outline">Belanja Sekarang</a>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter">
        <div class="container">
            <div class="newsletter-content">
                <h2>Dapatkan Promo & Info Terbaru</h2>
                <p>Berlangganan newsletter kami untuk mendapatkan diskon dan informasi produk terbaru</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Alamat email Anda" required>
                    <button type="submit" class="btn btn-primary">Berlangganan</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="logo-container">
                        <img src="logo.png" alt="Ira Beauty Logo" class="logo-img">
                        <div class="logo-text">Ira<span>Beauty</span></div>
                    </div>
                    <p class="footer-about">
                        Ira Beauty menyediakan produk skincare berkualitas dengan bahan alami untuk merawat kulit Anda.
                    </p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3>Tautan Cepat</h3>
                    <ul>
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="products.php">Produk</a></li>
                        <li><a href="about.php">Tentang Kami</a></li>
                        <li><a href="contact.php">Kontak</a></li>
                </div>
                <div class="footer-col">
                    <h3>Hubungi Kami</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> Jl. Raya Contoh No. 123, Jakarta</li>
                        <li><i class="fas fa-phone"></i> +62 812 3456 7890</li>
                        <li><i class="fas fa-envelope"></i> info@irabeauty.com</li>
                        <li><i class="fas fa-clock"></i> Buka setiap hari 09:00 - 17:00 WIB</li>
                    </ul>
                </div>
            </div>
        </div>
        </div>
    </footer>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3>Keranjang Belanja</h3>
            <button class="close-cart" id="closeCart"><i class="fas fa-times"></i></button>
        </div>
        <div class="cart-items" id="cartItems">
            <!-- Cart items will be loaded here -->
        </div>
        <div class="cart-summary">
            <div class="cart-total">
                <span>Total</span>
                <span id="cartTotal">Rp0</span>
            </div>
            <a href="checkout.php" class="btn btn-primary btn-block">Checkout</a>
            <a href="cart.php" class="btn btn-outline btn-block">Lihat Keranjang</a>
        </div>
    </div>
    <div class="cart-overlay" id="cartOverlay"></div>

    <script src="script.js"></script>
    <script>
    // Initialize cart functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Cart toggle
        const cartIcon = document.getElementById('cartIcon');
        const cartSidebar = document.getElementById('cartSidebar');
        const cartOverlay = document.getElementById('cartOverlay');
        const closeCart = document.getElementById('closeCart');

        cartIcon.addEventListener('click', function() {
            cartSidebar.classList.add('active');
            cartOverlay.classList.add('active');
            loadCartItems();
        });

        closeCart.addEventListener('click', function() {
            cartSidebar.classList.remove('active');
            cartOverlay.classList.remove('active');
        });

        cartOverlay.addEventListener('click', function() {
            cartSidebar.classList.remove('active');
            cartOverlay.classList.remove('active');
        });

        // Load cart items from localStorage
        function loadCartItems() {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            const cartItems = document.getElementById('cartItems');
            const cartTotal = document.getElementById('cartTotal');
            const cartCount = document.getElementById('cartCount');

            cartItems.innerHTML = '';

            if (cart.length === 0) {
                cartItems.innerHTML = '<p class="empty-cart">Keranjang belanja kosong</p>';
                cartTotal.textContent = 'Rp0';
                cartCount.textContent = '0';
                return;
            }

            let total = 0;

            cart.forEach((item, index) => {
                total += item.price * item.quantity;

                const cartItem = document.createElement('div');
                cartItem.className = 'cart-item';
                cartItem.innerHTML = `
                    <div class="cart-item-image">
                        <img src="uploads/products/${item.image}" alt="${item.name}">
                    </div>
                    <div class="cart-item-details">
                        <h4>${item.name}</h4>
                        <div class="cart-item-price">Rp${item.price.toLocaleString('id-ID')}</div>
                        <div class="cart-item-quantity">
                            <button class="quantity-btn minus" data-index="${index}">-</button>
                            <span>${item.quantity}</span>
                            <button class="quantity-btn plus" data-index="${index}">+</button>
                        </div>
                    </div>
                    <button class="remove-item" data-index="${index}">
                        <i class="fas fa-trash"></i>
                    </button>
                `;

                cartItems.appendChild(cartItem);
            });

            cartTotal.textContent = `Rp${total.toLocaleString('id-ID')}`;
            cartCount.textContent = cart.length;

            // Add event listeners for quantity buttons
            document.querySelectorAll('.quantity-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const index = this.getAttribute('data-index');
                    updateCartItem(index, this.classList.contains('plus') ? 1 : -1);
                });
            });

            // Add event listeners for remove buttons
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    const index = this.getAttribute('data-index');
                    removeCartItem(index);
                });
            });
        }

        function updateCartItem(index, change) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart[index].quantity += change;

            if (cart[index].quantity < 1) {
                cart[index].quantity = 1;
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            loadCartItems();
        }

        function removeCartItem(index) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCartItems();
        }

        // Add to cart buttons
        document.querySelectorAll('.btn-add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                addToCart(productId);
            });
        });

        function addToCart(productId) {
            // In a real implementation, you would fetch product details from the server
            // For now, we'll simulate adding to cart
            let cart = JSON.parse(localStorage.getItem('cart')) || [];

            // Check if product already in cart
            const existingItem = cart.find(item => item.id === productId);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                // This would normally come from an API call
                const product = {
                    id: productId,
                    name: 'Sample Product',
                    price: 100000,
                    image: 'default-product.jpg',
                    quantity: 1
                };

                cart.push(product);
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            loadCartItems();

            // Show notification
            showNotification('Produk telah ditambahkan ke keranjang');
        }

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
    });
    </script>
</body>

</html>