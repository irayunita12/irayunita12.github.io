<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '202312035');
define('DB_NAME', 'ira_beauty');

// Site Configuration
define('SITE_NAME', 'Ira Beauty');
define('SITE_URL', 'http://localhost/ira_beauty'); // Update with your actual URL
define('ADMIN_EMAIL', 'admin@irabeauty.com');

// Security Configuration
define('CSRF_TOKEN_EXPIRE', 3600); // 1 hour
define('PASSWORD_RESET_EXPIRE', 1800); // 30 minutes
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// File Upload Configuration
define('MAX_FILE_SIZE', 2097152); // 2MB
define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('UPLOAD_DIR', 'uploads/');
define('AVATAR_DIR', 'uploads/avatars/');
define('PRODUCT_IMAGE_DIR', 'uploads/products/');

// Session Configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('SESSION_REGENERATE', 300); // 5 minutes

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// Timezone Configuration
date_default_timezone_set('Asia/Jakarta');

// WhatsApp Configuration
define('ADMIN_WHATSAPP', '+6285651378535');
define('WHATSAPP_MESSAGE_PREFIX', 'Halo Admin Ira Beauty, saya ingin memesan:');

// Email Configuration
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_email@example.com');
define('SMTP_PASS', 'your_email_password');
define('SMTP_FROM', 'no-reply@irabeauty.com');
define('SMTP_FROM_NAME', 'Ira Beauty');

// Include other necessary files
require_once 'db.php';
require_once 'functions.php';
?>