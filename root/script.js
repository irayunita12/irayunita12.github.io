// script.js - Main JavaScript file for Ira Beauty application

document.addEventListener("DOMContentLoaded", function () {
  // Initialize cart from localStorage or create empty array
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  // Admin data for WhatsApp integration
  const adminData = {
    whatsapp: "+6285651378535",
    name: "Admin Ira Skincare",
  };

  // Initialize elements that exist on multiple pages
  const initCommonElements = () => {
    // Cart icon and dropdown (exists on most pages)
    const cartIcon = document.getElementById("cartIcon");
    const cartDropdown = document.getElementById("cartDropdown");
    const cartCount = document.getElementById("cartCount");
    const cartItems = document.getElementById("cartItems");
    const cartTotal = document.getElementById("cartTotal");
    const checkoutBtn = document.getElementById("checkoutBtn");

    if (cartIcon && cartDropdown) {
      cartIcon.addEventListener("click", (e) => {
        e.stopPropagation();
        cartDropdown.classList.toggle("active");
      });

      document.addEventListener("click", () => {
        cartDropdown.classList.remove("active");
      });
    }

    // Search functionality (exists on index.php and products.php)
    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
      searchInput.addEventListener("input", handleSearch);
    }

    // Update cart display on pages that have it
    if (cartCount) {
      updateCartDisplay();
    }

    // Checkout button
    if (checkoutBtn) {
      checkoutBtn.addEventListener("click", handleCheckout);
    }
  };

  // Update cart display in header/navbar
  const updateCartDisplay = () => {
    const cartCount = document.getElementById("cartCount");
    const cartItems = document.getElementById("cartItems");
    const cartTotal = document.getElementById("cartTotal");

    if (!cartCount) return;

    // Update cart count
    cartCount.textContent = cart.length;

    // Update cart items list
    if (cartItems) {
      cartItems.innerHTML = "";
      let total = 0;

      cart.forEach((item, index) => {
        total += item.price;

        const cartItem = document.createElement("div");
        cartItem.className = "cart-item";
        cartItem.innerHTML = `
                    <img src="${item.image_url}" alt="${item.title}">
                    <div class="cart-item-info">
                        <div class="cart-item-title">${item.title}</div>
                        <div class="cart-item-price">Rp${parseInt(
                          item.price
                        ).toLocaleString("id-ID")}</div>
                    </div>
                    <div class="remove-item" onclick="removeFromCart(${index})">✕</div>
                `;

        cartItems.appendChild(cartItem);
      });

      // Update total
      if (cartTotal) {
        cartTotal.textContent = total.toLocaleString("id-ID");
      }
    }

    // Save to localStorage
    localStorage.setItem("cart", JSON.stringify(cart));
  };

  // Handle search functionality
  const handleSearch = function () {
    const searchTerm = this.value.toLowerCase();
    const productCards = document.querySelectorAll(".product-card");

    if (productCards.length > 0) {
      productCards.forEach((card) => {
        const title = card
          .querySelector(".product-title")
          ?.textContent.toLowerCase();
        const brand = card
          .querySelector(".product-brand")
          ?.textContent.toLowerCase();

        if (title?.includes(searchTerm) || brand?.includes(searchTerm)) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    }
  };

  // Add to cart function
  window.addToCart = function (product) {
    cart.push({
      id: product.id,
      title: product.title,
      price: parseInt(product.price),
      image_url: product.image_url,
    });

    updateCartDisplay();
    showNotification(`${product.title} telah ditambahkan ke keranjang!`);
  };

  // Remove from cart function
  window.removeFromCart = function (index) {
    if (index >= 0 && index < cart.length) {
      cart.splice(index, 1);
      updateCartDisplay();
    }
  };

  // Handle checkout process
  const handleCheckout = function () {
    if (cart.length === 0) {
      showNotification("Keranjang belanja kosong!", "error");
      return;
    }

    let message = `Halo ${adminData.name}, saya ingin memesan:\n\n`;
    let totalHarga = 0;

    cart.forEach((item) => {
      message += `- ${item.title} (Rp${parseInt(item.price).toLocaleString(
        "id-ID"
      )})\n`;
      totalHarga += item.price;
    });

    message += `\n*Total Items:* ${cart.length}\n`;
    message += `*Subtotal:* Rp${totalHarga.toLocaleString("id-ID")}\n`;
    message += `*Ongkir:* (Konfirmasi admin)\n`;
    message += `*Total Pembayaran:* Rp${totalHarga.toLocaleString(
      "id-ID"
    )}\n\n`;
    message += `Silakan konfirmasi ketersediaan barang. Terima kasih!`;

    const encodedMessage = encodeURIComponent(message);
    const whatsappUrl = `https://wa.me/${adminData.whatsapp}?text=${encodedMessage}`;

    window.open(whatsappUrl, "_blank");
  };

  // Show notification
  const showNotification = (message, type = "success") => {
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;

    const icon =
      type === "success" ? "fa-check-circle" : "fa-exclamation-circle";
    notification.innerHTML = `
            <i class="fas ${icon}"></i>
            <span>${message}</span>
        `;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.classList.add("show");
    }, 10);

    setTimeout(() => {
      notification.classList.remove("show");
      setTimeout(() => {
        document.body.removeChild(notification);
      }, 300);
    }, 3000);
  };

  // Tab functionality for profile page
  const initProfileTabs = () => {
    const tabs = document.querySelectorAll(".tab");
    if (tabs.length > 0) {
      tabs.forEach((tab) => {
        tab.addEventListener("click", function () {
          // Hide all tab contents
          document.querySelectorAll(".tab-content").forEach((content) => {
            content.classList.remove("active");
          });

          // Deactivate all tabs
          tabs.forEach((t) => t.classList.remove("active"));

          // Activate current tab
          this.classList.add("active");

          // Show corresponding content
          const tabName = this.getAttribute("onclick").match(/'([^']+)'/)[1];
          document.getElementById(tabName).classList.add("active");
        });
      });
    }
  };

  // WhatsApp number formatting
  const initWhatsAppInput = () => {
    const whatsappInput = document.getElementById("whatsapp");
    if (whatsappInput) {
      whatsappInput.addEventListener("input", function (e) {
        this.value = this.value.replace(/[^0-9]/g, "");
      });
    }
  };

  // Initialize all functionality
  initCommonElements();
  initProfileTabs();
  initWhatsAppInput();
});

// Product grid display function (used in index.php)
window.displayProducts = function (products) {
  const productsGrid = document.getElementById("productsGrid");
  if (!productsGrid) return;

  productsGrid.innerHTML = "";

  if (products.length === 0) {
    productsGrid.innerHTML = `<p class="no-products">Tidak ada produk yang ditemukan.</p>`;
    return;
  }

  products.forEach((product) => {
    const productCard = document.createElement("div");
    productCard.className = "product-card";

    // Create skin type tags if they exist
    let skinTags = "";
    if (product.skinType && product.skinType.length > 0) {
      skinTags = `<div class="product-tags">
                ${product.skinType
                  .map((type) => `<span class="skin-tag">${type}</span>`)
                  .join("")}
            </div>`;
    }

    // Create original price display if it exists
    let originalPrice = "";
    if (product.originalPrice && product.originalPrice > 0) {
      originalPrice = `<span class="original-price">Rp${parseInt(
        product.originalPrice
      ).toLocaleString("id-ID")}</span>`;
    }

    // Create discount badge if it exists
    let discountBadge = "";
    if (product.discount && product.discount > 0) {
      discountBadge = `<span class="discount">${product.discount}%</span>`;
    }

    productCard.innerHTML = `
            <img src="${product.image_url}" alt="${
      product.title
    }" class="product-image">
            <div class="product-info">
                <h3 class="product-title">${product.title}</h3>
                <p class="product-brand">${product.brand}</p>
                ${skinTags}
                <div class="product-price">
                    <span class="current-price">Rp${parseInt(
                      product.price
                    ).toLocaleString("id-ID")}</span>
                    ${originalPrice}
                    ${discountBadge}
                </div>
                <button class="buy-btn" onclick="addToCart(${JSON.stringify(
                  product
                ).replace(/"/g, "&quot;")})">
                    <i class="fas fa-shopping-cart"></i> Beli
                </button>
            </div>
        `;

    productsGrid.appendChild(productCard);
  });
};

// Wishlist functions
if (typeof window.removeFromWishlist !== "function") {
  window.removeFromWishlist = function (productId) {
    if (
      confirm("Apakah Anda yakin ingin menghapus produk ini dari wishlist?")
    ) {
      fetch("wishlist_action.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `action=remove&product_id=${productId}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Remove item from DOM
            const itemElement = document.querySelector(
              `.wishlist-item[data-id="${productId}"]`
            );
            if (itemElement) {
              itemElement.remove();
            }

            showNotification("Produk telah dihapus dari wishlist");

            // If no more items, show empty state
            if (document.querySelectorAll(".wishlist-item").length === 0) {
              const wishlistGrid = document.querySelector(".wishlist-grid");
              if (wishlistGrid) {
                wishlistGrid.innerHTML = `
                                <div class="empty-wishlist">
                                    <i class="fas fa-heart"></i>
                                    <h3>Wishlist Anda kosong</h3>
                                    <p>Tambahkan produk favorit Anda ke wishlist untuk melihatnya di sini</p>
                                    <a href="products.php" class="btn">Jelajahi Produk</a>
                                </div>
                            `;
              }
            }
          } else {
            showNotification(
              "Gagal menghapus produk: " +
                (data.message || "Terjadi kesalahan"),
              "error"
            );
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          showNotification("Terjadi kesalahan saat menghapus produk", "error");
        });
    }
  };
}

// Order history functions
if (typeof window.handleReorder !== "function") {
  window.handleReorder = function (orderId) {
    // In a real application, you would fetch the order items and add them to cart
    showNotification(
      `Produk dari pesanan #${orderId} akan ditambahkan ke keranjang`
    );
    // Here you would typically make an AJAX call to get order items and add to cart
  };
}

if (typeof window.viewOrderDetails !== "function") {
  window.viewOrderDetails = function (orderId) {
    // In a real application, you would redirect to an order detail page
    window.location.href = `order_detail.php?id=${orderId}`;
  };
}
