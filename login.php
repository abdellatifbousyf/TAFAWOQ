```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/db.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '/TAFAWOQ');
}

$baseUrl = rtrim(BASE_URL, '/');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'المرجو ملء جميع الحقول.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'SELECT id, username, email, password, role
                 FROM users
                 WHERE email = ?
                 LIMIT 1'
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (
                $user &&
                password_verify($password, $user['password'])
            ) {
                session_regenerate_id(true);

                if ($user['role'] === 'admin') {
                    $_SESSION['admin'] = $user['id'];
                    $_SESSION['admin_name'] = $user['username'];

                    header('Location: ' . $baseUrl . '/admin/dashboard.php');
                    exit;
                }

                $_SESSION['user'] = $user['id'];
                $_SESSION['user_name'] = $user['username'];

                header('Location: ' . $baseUrl . '/user/dashboard.php');
                exit;
            }

            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة.';
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            $error = 'وقع مشكل أثناء تسجيل الدخول. حاول مرة أخرى.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<style>
.login-container {
    max-width: 440px;
    margin: 60px auto;
    padding: 30px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, .08);
    direction: rtl;
    text-align: right;
}

.login-container h2 {
    text-align: center;
    margin-bottom: 10px;
    color: #173b70;
}

.login-description {
    text-align: center;
    color: #666;
    margin-bottom: 25px;
}

.login-container label {
    display: block;
    margin: 15px 0 7px;
    font-weight: 600;
}

.login-container input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-sizing: border-box;
    font-size: 16px;
}

.login-container input:focus {
    outline: none;
    border-color: #2459a9;
    box-shadow: 0 0 0 3px rgba(36, 89, 169, .1);
}

.login-button {
    width: 100%;
    margin-top: 22px;
    padding: 13px;
    background: #2459a9;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

.login-button:hover {
    background: #173b70;
}

.login-error {
    padding: 12px;
    color: #842029;
    background: #f8d7da;
    border-radius: 8px;
    margin-bottom: 15px;
}

.login-register {
    text-align: center;
    margin-top: 22px;
}

.login-register a {
    color: #2459a9;
    font-weight: bold;
    text-decoration: none;
}
</style>

<main class="login-container">
    <h2>تسجيل الدخول</h2>
    <p class="login-description">
        مرحباً بك في منصة تفوق TAFAWOQ
    </p>

    <?php if ($error !== ''): ?>
        <div class="login-error" role="alert">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="on">
        <label for="email">البريد الإلكتروني</label>
        <input
            type="email"
            id="email"
            name="email"
            value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            placeholder="example@email.com"
            autocomplete="email"
            required
        >

        <label for="password">كلمة المرور</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="أدخل كلمة المرور"
            autocomplete="current-password"
            required
        >

        <button type="submit" class="login-button">
            تسجيل الدخول
        </button>
    </form>

    <div class="login-register">
        ليس لديك حساب؟
        <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/pages/register.php">
            إنشاء حساب جديد
        </a>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
```