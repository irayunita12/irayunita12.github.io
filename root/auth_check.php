<?php
session_start();

// 1. Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Anda harus login terlebih dahulu";
    header("Location: login.php");
    exit;
}

// 2. Auto logout setelah 30 menit tidak aktif
$inactive = 1800; // 30 menit dalam detik
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $inactive)) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update waktu aktivitas terakhir

// 3. Verifikasi role untuk halaman admin
$current_page = basename($_SERVER['PHP_SELF']);

// Daftar halaman yang khusus untuk admin
$admin_pages = [
    'admin_dashboard.php',
    'admin_products.php',
    'admin_orders.php'
];

if (in_array($current_page, $admin_pages) && $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// 4. Regenerate ID session untuk mencegah session fixation
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}
?>