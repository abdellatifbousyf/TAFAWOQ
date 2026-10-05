<?php
session_start();
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'الرجاء ملء جميع الحقول.';
    } elseif (strlen($password) < 6) {
        $error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.';
    } elseif ($password !== $confirm) {
        $error = 'كلمتا المرور غير متطابقتين.';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'البريد الإلكتروني مستخدم بالفعل.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            if ($stmt->execute([$username, $email, $hash])) {
                $success = 'تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.';
            } else {
                $error = 'حدث خطأ أثناء إنشاء الحساب.';
            }
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
                    <h2>✨ إنشاء حساب جديد</h2>
                    <p>انضم إلى آلاف الطلاب في منصة تفوق</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label>الاسم الكامل</label>
                        <input type="text" name="username" required placeholder="أدخل اسمك الكامل"
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" required placeholder="example@email.com" dir="ltr"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>كلمة المرور</label>
                        <input type="password" name="password" required placeholder="6 أحرف على الأقل">
                    </div>
                    <div class="form-group">
                        <label>تأكيد كلمة المرور</label>
                        <input type="password" name="confirm_password" required placeholder="أعد كتابة كلمة المرور">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">إنشاء الحساب</button>
                </form>

                <div class="auth-footer">
                    <p>لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>