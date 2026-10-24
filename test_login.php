<?php
require_once 'includes/db.php';

echo "<h2>اختبار حساب الأدمن</h2>";

$stmt = $pdo->query("SELECT id, username, email, role, password FROM users WHERE email = 'admin@tafawoq.ma'");
$user = $stmt->fetch();

if ($user) {
    echo "✅ الحساب موجود:<br>";
    echo "ID: " . $user['id'] . "<br>";
    echo "الاسم: " . $user['username'] . "<br>";
    echo "الإيميل: " . $user['email'] . "<br>";
    echo "الدور: " . $user['role'] . "<br>";
    echo "كلمة المرور (مشفرة): " . substr($user['password'], 0, 20) . "...<br><br>";
    
    // اختبار كلمة المرور
    $test = password_verify('admin123', $user['password']);
    if ($test) {
        echo "✅ كلمة المرور صحيحة!";
    } else {
        echo "❌ كلمة المرور غلط! جرب الحل 1.";
    }
} else {
    echo "❌ الحساب غير موجود! جرب create_admin.php";
}
?>