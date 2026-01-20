<?php
require_once 'db.php';

$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('new', 'processing', 'completed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
";

try {
    $pdo->exec($sql);
    echo "Database setup successfully!";

    // Seed admin if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $pdo->prepare("INSERT INTO users (username, password, role) VALUES ('admin', ?, 'admin')")->execute([$hashedPassword]);
        echo "\nAdmin user created (admin/admin123)";
    }
    // Seed products if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $products = [
            ['Pure Merino Scarf', 45.00, 'Ultra-soft merino wool scarf in charcoal grey.', 'https://images.unsplash.com/photo-1520903920243-00d872a2d1c9?auto=format&fit=crop&q=80&w=400'],
            ['Hand-Knitted Beanie', 25.00, 'Chunky knit beanie with a faux fur pom-pom.', 'https://images.unsplash.com/photo-1576871333019-220a9a4d327b?auto=format&fit=crop&q=80&w=400'],
            ['Woolen Baby Blanket', 65.00, 'Delicate and warm blanket for the little ones.', 'https://images.unsplash.com/photo-1522771935876-068305c2f354?auto=format&fit=crop&q=80&w=400'],
            ['Cozy Wool Socks', 18.50, 'Thick wool socks for winter nights.', 'https://images.unsplash.com/photo-1582719202047-76d3432ee623?auto=format&fit=crop&q=80&w=400']
        ];

        $stmtInsert = $pdo->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
        foreach ($products as $p) {
            $stmtInsert->execute($p);
        }
        echo "\nInitial products seeded.";
    }

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>