<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// جلب بيانات المستخدم من قاعدة البيانات
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$message = '';

// تعديل البيانات الشخصية
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // تحقق من عدم وجود اسم مستخدم أو بريد إلكتروني مكرر (عدا المستخدم الحالي)
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $checkStmt->execute([$username, $email, $_SESSION['user_id']]);
    if ($checkStmt->fetchColumn() > 0) {
        $message = '<div class="alert alert-danger">اسم المستخدم أو البريد الإلكتروني مستخدم بالفعل.</div>';
    } else {
        $updateStmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, phone = ? WHERE id = ?");
        if ($updateStmt->execute([$full_name, $username, $email, $phone, $_SESSION['user_id']])) {
            $message = '<div class="alert alert-success">تم تحديث البيانات بنجاح.</div>';
            $_SESSION['full_name'] = $full_name;
            $_SESSION['username'] = $username;
        } else {
            $message = '<div class="alert alert-danger">حدث خطأ أثناء تحديث البيانات.</div>';
        }
    }
    // جلب البيانات المحدثة
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// تغيير كلمة المرور
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!password_verify($current_password, $user['password'])) {
        $message = '<div class="alert alert-danger">كلمة المرور الحالية غير صحيحة.</div>';
    } elseif ($new_password !== $confirm_password) {
        $message = '<div class="alert alert-danger">كلمة المرور الجديدة غير متطابقة.</div>';
    } elseif (strlen($new_password) < 6) {
        $message = '<div class="alert alert-danger">كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل.</div>';
    } else {
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        $updatePassStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($updatePassStmt->execute([$hashedPassword, $_SESSION['user_id']])) {
            $message = '<div class="alert alert-success">تم تغيير كلمة المرور بنجاح.</div>';
        } else {
            $message = '<div class="alert alert-danger">حدث خطأ أثناء تغيير كلمة المرور.</div>';
        }
    }
}

// جلب الطلبات
$orderStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orderStmt->execute([$_SESSION['user_id']]);
$orders = $orderStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة المستخدم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f8f9fa; }
        .profile-card { border: none; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .welcome-header { background: linear-gradient(45deg, #007bff, #0056b3); color: white; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-user"></i> لوحة المستخدم</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-5">
        <?php echo $message; ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card profile-card">
                    <div class="card-header welcome-header text-center">
                        <h3><i class="fas fa-user-circle"></i> مرحباً بك، <?php echo htmlspecialchars($user['full_name']); ?></h3>
                    </div>
                    <div class="card-body p-5">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-info-circle"></i> معلومات الحساب</h5>
                                <hr>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">الاسم الكامل</label>
                                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">اسم المستخدم</label>
                                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">البريد الإلكتروني</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">رقم الهاتف</label>
                                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
                                    </div>
                                    <button type="submit" name="update_profile" class="btn btn-primary w-100 mb-2">
                                        <i class="fas fa-edit"></i> حفظ التعديلات
                                    </button>
                                </form>
                                <p class="mt-3"><strong>تاريخ التسجيل:</strong> <?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-key"></i> تغيير كلمة المرور</h5>
                                <hr>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">كلمة المرور الحالية</label>
                                        <input type="password" name="current_password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">كلمة المرور الجديدة</label>
                                        <input type="password" name="new_password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">تأكيد كلمة المرور الجديدة</label>
                                        <input type="password" name="confirm_password" class="form-control" required>
                                    </div>
                                    <button type="submit" name="change_password" class="btn btn-success w-100">
                                        <i class="fas fa-key"></i> تغيير كلمة المرور
                                    </button>
                                </form>
                                <a href="index.php" class="btn btn-info w-100 mt-3">
                                    <i class="fas fa-home"></i> العودة للمتجر
                                </a>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6><i class="fas fa-bell"></i> ملاحظات مهمة:</h6>
                            <ul class="mb-0">
                                <li>يمكنك الآن تصفح المتجر والتسوق</li>
                                <li>ستحصل على إشعارات للعروض الخاصة</li>
                                <li>يمكنك تتبع طلباتك من هنا</li>
                            </ul>
                        </div>

                        <h5 class="mt-4">طلباتك السابقة:</h5>
                        <?php foreach ($orders as $order): ?>
                            <div class="mb-3 p-2 border rounded">
                                <strong>طلب رقم: <?php echo $order['id']; ?></strong> - <span><?php echo $order['created_at']; ?></span><br>
                                <?php
                                $itemsStmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                                $itemsStmt->execute([$order['id']]);
                                $items = $itemsStmt->fetchAll();
                                foreach ($items as $item):
                                ?>
                                    <?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?> ($<?php echo number_format($item['price'], 2); ?>)<br>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['order_success'])): ?>
      <div class="alert alert-success text-center">
        تم طلب الأوردر بنجاح!
      </div>
      <?php unset($_SESSION['order_success']); ?>
    <?php endif; ?>
</body>
</html>
