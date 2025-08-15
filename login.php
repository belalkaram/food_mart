
<?php
session_start();
require_once 'config.php';

$message = '';

if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $message = '<div class="alert alert-danger">اسم المستخدم وكلمة المرور مطلوبان</div>';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                
                if ($user['role'] === 'admin') {
                    header('Location: admin_dashboard.php');
                } else {
                    header('Location: user_dashboard.php');
                }
                exit;
            } else {
                $message = '<div class="alert alert-danger">اسم المستخدم أو كلمة المرور خاطئة</div>';
            }
        } catch(PDOException $e) {
            $message = '<div class="alert alert-danger">خطأ في تسجيل الدخول</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f8f9fa; }
        .login-container { max-width: 400px; margin: 100px auto; }
        .card { border: none; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(45deg, #28a745, #20c997); color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card">
                <div class="card-header text-center">
                    <h3>تسجيل الدخول</h3>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">اسم المستخدم أو البريد الإلكتروني</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 mb-3">دخول</button>
                        
                        <div class="text-center">
                            <a href="register.php">إنشاء حساب جديد</a>
                        </div>
                        
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
