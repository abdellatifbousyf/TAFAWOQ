<?php
require_once 'includes/db.php';

echo "<!DOCTYPE html><html dir='rtl'><head><meta charset='UTF-8'><title>فحص الأدمن</title></head><body>";
echo "<h2>فحص حساب الأدمن</h2>";

// حذف الحساب القديم
$pdo->exec("DELETE FROM users WHERE email = 'admin@tafawoq.ma'");

// إنشاء كلمة مرور مشفرة
$password = password_hash('admin123', PASSWORD_DEFAULT);

// إضافة الحساب
$stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
$stmt->execute(['المدير', 'admin@tafawoq.ma', $password]);

echo "<p style='color:green;font-size:1.2rem;'>✅ تم إنشاء حساب الأدمن بنجاح!</p>";
echo "<p><strong>الإيميل:</strong> admin@tafawoq.ma</p>";
echo "<p><strong>كلمة المرور:</strong> admin123</p>";
echo "<br><a href='login.php' style='padding:10px 20px;background:#1a73e8;color:white;text-decoration:none;border-radius:5px;'>الذهاب لتسجيل الدخول</a>";
echo "</body></html>";
?>