<?php
require_once 'db.php';
session_start();

// Simple auth (for production use proper password verify)
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin'] = true;
    } else {
        $error = "Invalid credentials";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

if (!isset($_SESSION['admin'])):
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Admin Login - Woolly Wonderland</title>
        <link rel="stylesheet" href="assets/css/style.css">
        <style>
            .login-container {
                max-width: 400px;
                margin: 100px auto;
                padding: 40px;
                background: white;
                border-radius: 20px;
                box-shadow: var(--shadow);
            }

            .form-group {
                margin-bottom: 20px;
            }

            input {
                width: 100%;
                padding: 12px;
                border: 1px solid #ddd;
                border-radius: 10px;
            }

            .error {
                color: red;
                margin-bottom: 20px;
            }
        </style>
    </head>

    <body style="background: #f0f2f5;">
        <div class="login-container">
            <h2 style="text-align: center; margin-bottom: 30px;">Admin Login</h2>
            <?php if (isset($error))
                echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>
        </div>
    </body>

    </html>
<?php else:
    // Admin Dashboard
    if (isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $desc = $_POST['description'];
        $img = $_POST['image_url']; // For simplicity, using URL instead of upload

        $stmt = $pdo->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $price, $desc, $img]);
        $msg = "Product added!";
    }

    $products = $pdo->query("SELECT * FROM products")->fetchAll();
    $orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Admin Dashboard - Woolly Wonderland</title>
        <link rel="stylesheet" href="assets/css/style.css">
        <style>
            .dashboard {
                padding: 50px 0;
            }

            .card {
                background: white;
                padding: 30px;
                border-radius: 20px;
                margin-bottom: 30px;
                box-shadow: var(--shadow);
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                text-align: left;
                padding: 15px;
                border-bottom: 1px solid #eee;
            }

            .status-badge {
                padding: 5px 10px;
                border-radius: 5px;
                font-size: 0.8rem;
            }

            .new {
                background: #e3f2fd;
                color: #1976d2;
            }
        </style>
    </head>

    <body>
        <nav class="navbar">
            <div class="container">
                <a href="index.php" class="logo">Woolly<span>Admin</span></a>
                <a href="?logout=1">Logout</a>
            </div>
        </nav>

        <div class="container dashboard">
            <h1>Dashboard</h1>

            <div class="card">
                <h2>Add New Product</h2>
                <?php if (isset($msg))
                    echo "<p style='color: green;'>$msg</p>"; ?>
                <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <input type="text" name="name" placeholder="Product Name" required>
                    <input type="number" step="0.01" name="price" placeholder="Price" required>
                    <input type="text" name="image_url" placeholder="Image URL (Unsplash link)">
                    <textarea name="description" placeholder="Description"
                        style="grid-column: span 2; padding: 10px; height: 100px; border-radius: 10px; border: 1px solid #ddd;"></textarea>
                    <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                </form>
            </div>

            <div class="card">
                <h2>Recent Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#
                                    <?php echo $order['id']; ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($order['customer_name']); ?>
                                </td>
                                <td>$
                                    <?php echo $order['total_price']; ?>
                                </td>
                                <td><span class="status-badge <?php echo $order['status']; ?>">
                                        <?php echo $order['status']; ?>
                                    </span></td>
                                <td>
                                    <?php echo $order['created_at']; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>

    </html>
<?php endif; ?>