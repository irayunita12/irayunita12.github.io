<?php
require 'auth_check.php';
require 'db.php';

// Only allow admin users to access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if (isset($_POST['add_user'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $whatsapp = preg_replace('/[^0-9]/', '', $_POST['whatsapp']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validation
    if (empty($nama) || empty($email) || empty($whatsapp) || empty($password)) {
        $error = 'All fields are required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format!';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters!';
    } else {
        // Check if email already exists
        $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $error = 'Email already registered!';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $query = "INSERT INTO users (nama, email, password, whatsapp, role) 
                      VALUES ('$nama', '$email', '$hashed_password', '$whatsapp', '$role')";
            
            if (mysqli_query($conn, $query)) {
                $success = 'User added successfully!';
                // Clear form
                $_POST = array();
            } else {
                $error = 'Error adding user: ' . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Ira Beauty</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: #f5f9ff;
        color: #333;
    }

    .container {
        max-width: 800px;
        margin: 30px auto;
        padding: 30px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    h1 {
        color: #6b8cff;
        margin-bottom: 20px;
        text-align: center;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #555;
        font-weight: 500;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: #6b8cff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(107, 140, 255, 0.2);
    }

    .btn {
        padding: 12px 25px;
        background: #6b8cff;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn:hover {
        background: #5a7ae5;
    }

    .alert {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-error {
        background: #ffebee;
        color: #ff4444;
    }

    .alert-success {
        background: #e8f5e9;
        color: #4CAF50;
    }

    .back-link {
        display: inline-block;
        margin-top: 20px;
        color: #6b8cff;
        text-decoration: none;
    }

    .back-link:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Add New User</h1>

        <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nama">Full Name</label>
                <input type="text" id="nama" name="nama"
                    value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label for="whatsapp">WhatsApp Number</label>
                <input type="tel" id="whatsapp" name="whatsapp"
                    value="<?= isset($_POST['whatsapp']) ? htmlspecialchars($_POST['whatsapp']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="user" <?= isset($_POST['role']) && $_POST['role'] === 'user' ? 'selected' : '' ?>>
                        User</option>
                    <option value="admin" <?= isset($_POST['role']) && $_POST['role'] === 'admin' ? 'selected' : '' ?>>
                        Admin</option>
                </select>
            </div>

            <button type="submit" name="add_user" class="btn">Add User</button>
        </form>

        <a href="admin_dashboard.php" class="back-link">← Back to Admin Dashboard</a>
    </div>

    <script>
    // Format WhatsApp number (digits only)
    document.getElementById('whatsapp').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    </script>
</body>

</html>