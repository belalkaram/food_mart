<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        
        if ($_POST['action'] === 'add') {
            $productId = $_POST['product_id'];
            $productName = $_POST['name'];
            $productPrice = (float)$_POST['price']; // Convert price to float
            $quantity = (int)$_POST['quantity'];

            if ($quantity > 0 && !empty($productId) && !empty($productName)) {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$productId] = [
                        'name'     => $productName,
                        'price'    => $productPrice,
                        'quantity' => $quantity
                    ];
                }
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success'    => true,
                    'message'    => 'Product added to cart successfully!',
                    'cart_count' => count($_SESSION['cart'])
                ]);
                exit;
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid Request.']);