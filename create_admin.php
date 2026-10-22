```php
<?php
session_start();

require_once __DIR__ . '/includes/db.php';

$username = 'admin';
$email = 'admin@tafawoq.ma';
$password = 'admin123';

try {
    $stmt = $pdo->prepare(
        "SELECT id FROM users WHERE email = ? LIMIT 1"
    );
    $stmt->execute([$email]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if ($existingUser) {
        $stmt = $pdo->prepare(
            "UPDATE users
             SET username = ?, password = ?, role = 'admin'
             WHERE id = ?"
        );
        $stmt->execute([
            $username,
            $hashedPassword,
            $existingUser['id']
        ]);
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO users (username, email, password, role)
             VALUES (?, ?, ?, 'admin')"
        );
        $stmt->execute([$username, $email, $hashedPassword]);
    }

    echo "تم إنشاء أو تحديث حساب المدير بنجاح.<br>";
    echo "الإيميل: " . htmlspecialchars($email) . "<br>";
    echo "كلمة المرور: " . htmlspecialchars($password) . "<br>";
    echo '<a href="/TAFAWOQ/login.php">الدخول إلى الموقع</a>';

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo "وقع خطأ أثناء إنشاء الحساب. تحقق من إعدادات قاعدة البيانات.";
}
?>
```