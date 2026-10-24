<?php
// للتجربة فقط على localhost. احذف الملف قبل رفع الموقع.
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Forbidden');
}
require_once __DIR__ . '/includes/db.php';

header('Content-Type: text/html; charset=UTF-8');
echo "<div dir='rtl' style='font-family:sans-serif'><h2>اختبار حساب الأدمن</h2>";

$stmt = $pdo->prepare('SELECT id, username, email, role, password FROM users WHERE email = ?');
$stmt->execute(['admin@tafawoq.ma']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "✅ الحساب موجود:<br>";
    echo "ID: " . (int) $user['id'] . "<br>";
    echo "الاسم: " . htmlspecialchars($user['username']) . "<br>";
    echo "الإيميل: " . htmlspecialchars($user['email']) . "<br>";
    echo "الدور: " . htmlspecialchars($user['role']) . "<br>";
    echo "كلمة المرور (مشفرة): " . htmlspecialchars(substr($user['password'], 0, 20)) . "...<br><br>";
    echo password_verify('admin123', $user['password'])
        ? "✅ كلمة المرور صحيحة!"
        : "❌ كلمة المرور غلط! شغّل check_admin.php أو create_admin.php.";
} else {
    echo "❌ الحساب غير موجود! شغّل create_admin.php";
}
echo "</div>";