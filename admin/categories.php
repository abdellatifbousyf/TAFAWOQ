<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$message = '';

// حذف
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 4) { // حماية الأقسام الافتراضية
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        $message = 'تم حذف القسم.';
    } else {
        $message = 'لا يمكن حذف الأقسام الافتراضية.';
    }
}

// إضافة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cat'])) {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $icon = trim($_POST['icon']);
    $desc = trim($_POST['description']);

    if ($name && $slug) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, icon, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $icon, $desc]);
        $message = 'تم إضافة القسم بنجاح!';
    }
}

$categories = $pdo->query("SELECT c.*, COUNT(e.id) as exam_count FROM categories c LEFT JOIN exams e ON c.id = e.category_id GROUP BY c.id ORDER BY c.id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الأقسام - تفوق</title>
    <link rel="stylesheet" href="/Tafawoq/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
</head>
<body>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <h3>🏆 تفوّق</h3>
        <ul>
            <li><a href="dashboard.php">📊 لوحة التحكم</a></li>
            <li><a href="exams.php">📝 الامتحانات</a></li>
            <li><a href="categories.php" class="active">📂 الأقسام</a></li>
            <li><a href="users.php">👥 المستخدمين</a></li>
            <li><a href="/Tafawoq/index.php">🌐 عرض الموقع</a></li>
            <li><a href="login.php?logout=1" style="color:#ff6b6b;">🚪 خروج</a></li>
        </ul>
    </aside>

    <main class="admin-content">
        <h2>📂 إدارة الأقسام</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>

        <div class="admin-form" style="margin-bottom:30px;">
            <h3 style="margin-bottom:15px;">إضافة قسم جديد</h3>
            <form method="POST">
                <div class="form-group">
                    <label>اسم القسم *</label>
                    <input type="text" name="name" required placeholder="مثال: الابتدائي">
                </div>
                <div class="form-group">
                    <label>الرابط (Slug) *</label>
                    <input type="text" name="slug" required placeholder="مثال: primary" dir="ltr">
                </div>
                <div class="form-group">
                    <label>الأيقونة (Emoji)</label>
                    <input type="text" name="icon" placeholder="📖" maxlength="4">
                </div>
                <div class="form-group">
                    <label>الوصف</label>
                    <textarea name="description" rows="2" placeholder="وصف مختصر للقسم..."></textarea>
                </div>
                <button type="submit" name="add_cat" class="btn btn-success">➕ إضافة</button>
            </form>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الأيقونة</th>
                    <th>الاسم</th>
                    <th>Slug</th>
                    <th>عدد الامتحانات</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td style="font-size:1.5rem;"><?= $cat['icon'] ?: '📁' ?></td>
                        <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                        <td dir="ltr"><?= htmlspecialchars($cat['slug']) ?></td>
                        <td><?= $cat['exam_count'] ?></td>
                        <td>
                            <?php if ($cat['id'] > 4): ?>
                                <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" data-confirm="حذف القسم؟">🗑️</a>
                            <?php else: ?>
                                <span style="color:var(--gray-500);font-size:0.85rem;">افتراضي</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>

<script src="/Tafawoq/script.js"></script>
</body>
</html>