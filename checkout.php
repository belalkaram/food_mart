<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

// حفظ الطلب
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("INSERT INTO orders (user_id) VALUES (?)");
    $stmt->execute([$_SESSION['user_id']]);
    $order_id = $pdo->lastInsertId();

    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $product_id, $item['quantity'], $item['price']]);
    }

    $pdo->commit();
    $_SESSION['cart'] = [];
    $_SESSION['order_success'] = true;
    header('Location: user_dashboard.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['order_success'] = false;
    header('Location: user_dashboard.php');
    exit;
}
?>