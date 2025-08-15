<?php
session_start();
require_once 'config.php';

// تم إلغاء التحقق من دور الأدمن، أي مستخدم مسجل دخول يمكنه الدخول للوحة الأدمن
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// حذف مستخدم
if (isset($_GET['delete_user'])) {
    $deleteId = intval($_GET['delete_user']);
    // لا يمكن حذف الأدمن نفسه
    if ($deleteId !== $_SESSION['user_id']) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$deleteId]);
    }
    header('Location: admin_dashboard.php');
    exit;
}

// حذف طلب
if (isset($_GET['delete_order'])) {
    $orderId = intval($_GET['delete_order']);
    $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$orderId]);
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$orderId]);
    header('Location: admin_dashboard.php');
    exit;
}

// جلب كل المستخدمين
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// جلب كل الطلبات
$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم الأدمن</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .table th, .table td { vertical-align: middle !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-user-shield"></i> لوحة تحكم الأدمن</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
                <a class="nav-link" href="index.php"><i class="fas fa-home"></i> العودة للمتجر</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <h2 class="mb-4">كل المستخدمين</h2>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>الاسم الكامل</th>
                    <th>اسم المستخدم</th>
                    <th>البريد الإلكتروني</th>
                    <th>رقم الهاتف</th>
                    <th>الدور</th>
                    <th>تاريخ التسجيل</th>
                    <th>حذف</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td><?php echo $user['created_at']; ?></td>
                    <td>
                        <?php if ($user['role'] !== 'admin'): ?>
                        <a href="?delete_user=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                            <i class="fas fa-trash"></i>
                        </a>
                        <?php else: ?>
                        <span class="text-muted">لا يمكن حذف الأدمن</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2 class="mb-4 mt-5">كل الطلبات</h2>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>المستخدم</th>
                    <th>تاريخ الطلب</th>
                    <th>المنتجات</th>
                    <th>حذف</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td>
                        <?php
                        $u = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
                        $u->execute([$order['user_id']]);
                        $userName = $u->fetchColumn();
                        echo htmlspecialchars($userName);
                        ?>
                    </td>
                    <td><?php echo $order['created_at']; ?></td>
                    <td>
                        <?php
                        $itemsStmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                        $itemsStmt->execute([$order['id']]);
                        $items = $itemsStmt->fetchAll();
                        foreach ($items as $item) {
                            echo htmlspecialchars($item['name']) . ' × ' . $item['quantity'] . ' ($' . number_format($item['price'], 2) . ')<br>';
                        }
                        ?>
                    </td>
                    <td>
                        <a href="?delete_order=<?php echo $order['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>