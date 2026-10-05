<?php
require_once 'includes/db.php';

echo "<h2>فحص حساب الأدمن</h2>";

// شوف جميع المستخدمين
$stmt = $pdo->query("SELECT id, username, email, role FROM users");
$users = $stmt->fetchAll();

if (empty($users)) {
    echo "<p style='color:red;'>❌ ما كاين حتى مستخدم في قاعدة البيانات!</p>";
    echo "<p>غادي نخلقو حساب الأدمن دابا...</p>";
    
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT INTO users (username, email, password, role) VALUES ('المدير', 'admin@tafawoq.ma', '$password', 'admin')");
    echo "<p style='color:green;'>✅ تم إنشاء الحساب!</p>";
} else {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>الاسم</th><th>الإيميل</th><th>الدور</th></tr>";
    foreach ($users as $u) {
        echo "<tr>";
        echo "<td>{$u['id']}</td>";
        echo "<td>{$u['username']}</td>";
        echo "<td>{$u['email']}</td>";
        echo "<td>{$u['role']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<br><a href='login.php'>الذهاب لتسجيل الدخول</a>";
?>