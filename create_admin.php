<?php
require_once 'includes/db.php';

// حذف الحساب القديم
$pdo->exec("DELETE FROM users WHERE email = 'admin@tafawoq.ma'");

// إنشاء كلمة مرور مشفرة
$password = password_hash('admin123', PASSWORD_DEFAULT);

// إضافة الحساب
$stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
$stmt->execute(['المدير', 'admin@tafawoq.ma', $password]);

echo "✅ تم إنشاء حساب الأدمن بنجاح!<br><br>";
echo "<strong>الإيميل:</strong> admin@tafawoq.ma<br>";
echo "<strong>كلمة المرور:</strong> admin123<br><br>";
echo '<a href="login.php">اضغط هنا لتسجيل الدخول</a>';
?>