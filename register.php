
<?php
session_start();
require_once 'config.php';

$message = '';

if ($_POST) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $message = '<div class="alert alert-danger">جميع الحقول مطلوبة</div>';
    } elseif ($password !== $confirm_password) {
        $message = '<div class="alert alert-danger">كلمات المرور غير متطابقة</div>';
    } elseif (strlen($password) < 6) {
        $message = '<div class="alert alert-danger">كلمة المرور يجب أن تكون 6 أحرف على الأقل</div>';
    } else {
        try {
            // التحقق من عدم وجود المستخدم
            $checkUser = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $checkUser->execute([$username, $email]);
            
            if ($checkUser->fetchColumn() > 0) {
                $message = '<div class="alert alert-danger">اسم المستخدم أو البريد الإلكتروني موجود مسبقاً</div>';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $insertUser = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)");
                
                if ($insertUser->execute([$username, $email, $hashedPassword, $full_name, $phone])) {
                    $message = '<div class="alert alert-success">تم التسجيل بنجاح! <a href="login.php">تسجيل الدخول</a></div>';
                }
            }
        } catch(PDOException $e) {
            $message = '<div class="alert alert-danger">خطأ في التسجيل: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل حساب جديد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f8f9fa; }
        .register-container { max-width: 500px; margin: 50px auto; }
        .card { border: none; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(45deg, #007bff, #0056b3); color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="card">
                <div class="card-header text-center">
                    <h3>تسجيل حساب جديد</h3>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">الاسم الكامل</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">اسم المستخدم</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">تسجيل</button>
                        
                        <div class="text-center">
                            <a href="login.php">لديك حساب؟ تسجيل الدخول</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
