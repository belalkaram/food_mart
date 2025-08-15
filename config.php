<?php

$host = $_ENV['DB_HOST'] ?? 'localhost';
$port = $_ENV['DB_PORT'] ?? '3306';
$dbname = $_ENV['DB_DATABASE'] ?? 'food';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // إنشاء جدول المستخدمين إذا لم يكن موجود
    $createUsersTable = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            role VARCHAR(20) DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";
    $pdo->exec($createUsersTable);

    // إنشاء جدول المنتجات إذا لم يكن موجود
    $createProductsTable = "
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            image VARCHAR(255),
            description TEXT
        )
    ";
    $pdo->exec($createProductsTable);

    // إنشاء جدول الطلبات إذا لم يكن موجود
    $createOrdersTable = "
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";
    $pdo->exec($createOrdersTable);

    // إنشاء جدول عناصر الطلب إذا لم يكن موجود
    $createOrderItemsTable = "
        CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            price DECIMAL(10,2) NOT NULL
        )
    ";
    $pdo->exec($createOrderItemsTable);

    // إنشاء حساب الأدمن الافتراضي إذا لم يكن موجود
    $adminCheck = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    if ($adminCheck->fetchColumn() == 0) {
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $insertAdmin = "INSERT INTO users (username, email, password, full_name, role) 
                       VALUES ('admin', 'admin@example.com', ?, 'المدير العام', 'admin')";
        $stmt = $pdo->prepare($insertAdmin);
        $stmt->execute([$adminPassword]);
    }

} catch(PDOException $e) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>
