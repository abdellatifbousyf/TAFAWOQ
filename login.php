<?php
session_start();
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'الرجاء ملء جميع الحقول.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['role'] === 'admin') {
                $_SESSION['admin'] = $user['id'];
                $_SESSION['admin_name'] = $user['username'];
                header('Location: admin/dashboard.php');
            } else {
                $_SESSION['user'] = $user['id'];
                $_SESSION['user_name'] = $user['username'];
                header('Location: user/dashboard.php');
            }
            exit;
        } else {
            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة.';
        }
    }
}

require_once 'includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h2>🔐 تسجيل الدخول</h2>
                    <p>مرحباً بعودتك! سجل دخولك للمتابعة</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" required placeholder="example@email.com" dir="ltr"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>كلمة المرور</label>
                        <input type="password" name="password" required placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">دخول</button>
                </form>

                <div class="auth-footer">
                    <p>ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>