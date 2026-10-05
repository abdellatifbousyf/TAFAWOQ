<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$totalExams = $pdo->query("SELECT COUNT(*) FROM exams")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalDownloads = $pdo->query("SELECT SUM(downloads) FROM exams")->fetchColumn() ?: 0;

$recentExams = $pdo->query("SELECT e.*, c.name as category_name FROM exams e LEFT JOIN categories c ON e.category_id = c.id ORDER BY e.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - تفوق</title>
    <link rel="stylesheet" href="/Tafawoq/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
</head>
<body>

<div class="admin-layout">
    <!-- الشريط الجانبي -->
    <aside class="admin-sidebar">
        <h3>🏆 تفوّق</h3>
        <ul>
            <li><a href="dashboard.php" class="active">📊 لوحة التحكم</a></li>
            <li><a href="exams.php">📝 الامتحانات</a></li>
            <li><a href="categories.php">📂 الأقسام</a></li>
            <li><a href="users.php">👥 المستخدمين</a></li>
            <li><a href="/Tafawoq/index.php">🌐 عرض الموقع</a></li>
            <li><a href="login.php?logout=1" style="color:#ff6b6b;">🚪 تسجيل الخروج</a></li>
        </ul>
    </aside>

    <!-- المحتوى -->
    <main class="admin-content">
        <h2>مرحباً، <?= htmlspecialchars($_SESSION['admin_name']) ?> 👋</h2>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div class="stat-value"><?= $totalExams ?></div>
                <div class="stat-title">امتحان و درس</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📂</div>
                <div class="stat-value"><?= $totalCategories ?></div>
                <div class="stat-title">قسم</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?= $totalUsers ?></div>
                <div class="stat-title">مستخدم</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⬇️</div>
                <div class="stat-value"><?= number_format($totalDownloads) ?></div>
                <div class="stat-title">تحميل</div>
            </div>
        </div>

        <h3 style="margin-bottom:15px;">آخر الامتحانات المضافة</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>العنوان</th>
                    <th>القسم</th>
                    <th>السنة</th>
                    <th>التحميلات</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentExams as $exam): ?>
                    <tr>
                        <td><?= $exam['id'] ?></td>
                        <td><?= htmlspecialchars($exam['title']) ?></td>
                        <td><?= htmlspecialchars($exam['category_name'] ?? '-') ?></td>
                        <td><?= $exam['year'] ?></td>
                        <td><?= $exam['downloads'] ?></td>
                        <td><?= date('Y/m/d', strtotime($exam['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>

<?php
// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
</body>
</html>