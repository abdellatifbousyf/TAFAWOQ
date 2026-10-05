<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$msgType = 'success';

// حذف
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $exam = $pdo->prepare("SELECT file_path FROM exams WHERE id = ?");
    $exam->execute([$id]);
    $row = $exam->fetch();
    if ($row && $row['file_path']) {
        @unlink('../uploads/' . $row['file_path']);
    }
    $pdo->prepare("DELETE FROM exams WHERE id = ?")->execute([$id]);
    $message = 'تم حذف الامتحان بنجاح.';
}

// إضافة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_exam'])) {
    $title = trim($_POST['title']);
    $category_id = (int)$_POST['category_id'];
    $year = (int)$_POST['year'];
    $type = $_POST['type'];
    $file_path = '';

    if (!empty($_FILES['file']['name'])) {
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'png'];
        if (in_array(strtolower($ext), $allowed)) {
            $filename = 'exam_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $uploadDir = '../uploads/exams/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $filename)) {
                $file_path = 'exams/' . $filename;
            }
        } else {
            $message = 'نوع الملف غير مسموح.';
            $msgType = 'danger';
        }
    }

    if (empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO exams (title, category_id, year, type, file_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $category_id, $year, $type, $file_path]);
        $message = 'تم إضافة الامتحان بنجاح!';
    }
}

$exams = $pdo->query("SELECT e.*, c.name as category_name FROM exams e LEFT JOIN categories c ON e.category_id = c.id ORDER BY e.created_at DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الامتحانات - تفوق</title>
    <link rel="stylesheet" href="/Tafawoq/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
</head>
<body>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <h3>🏆 تفوّق</h3>
        <ul>
            <li><a href="dashboard.php">📊 لوحة التحكم</a></li>
            <li><a href="exams.php" class="active">📝 الامتحانات</a></li>
            <li><a href="categories.php">📂 الأقسام</a></li>
            <li><a href="users.php">👥 المستخدمين</a></li>
            <li><a href="/Tafawoq/index.php">🌐 عرض الموقع</a></li>
            <li><a href="login.php?logout=1" style="color:#ff6b6b;">🚪 خروج</a></li>
        </ul>
    </aside>

    <main class="admin-content">
        <h2>📝 إدارة الامتحانات</h2>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType ?>"><?= $message ?></div>
        <?php endif; ?>

        <!-- نموذج الإضافة -->
        <div class="admin-form" style="margin-bottom:30px;">
            <h3 style="margin-bottom:15px;">إضافة امتحان جديد</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>العنوان *</label>
                    <input type="text" name="title" required placeholder="مثال: الامتحان الوطني 2025 - رياضيات">
                </div>
                <div class="form-group">
                    <label>القسم *</label>
                    <select name="category_id" required>
                        <option value="">اختر القسم</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                    <div class="form-group">
                        <label>السنة *</label>
                        <input type="number" name="year" required value="2025" min="2000" max="2030">
                    </div>
                    <div class="form-group">
                        <label>النوع *</label>
                        <select name="type" required>
                            <option value="exam">امتحان</option>
                            <option value="correction">تصحيح</option>
                            <option value="lesson">درس</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>الملف (PDF, DOC, IMG)</label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.png">
                </div>
                <button type="submit" name="add_exam" class="btn btn-success">➕ إضافة</button>
            </form>
        </div>

        <!-- الجدول -->
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>العنوان</th>
                    <th>القسم</th>
                    <th>السنة</th>
                    <th>النوع</th>
                    <th>الملف</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><?= $exam['id'] ?></td>
                        <td><?= htmlspecialchars($exam['title']) ?></td>
                        <td><?= htmlspecialchars($exam['category_name'] ?? '-') ?></td>
                        <td><?= $exam['year'] ?></td>
                        <td><?= $exam['type'] == 'exam' ? 'امتحان' : ($exam['type'] == 'correction' ? 'تصحيح' : 'درس') ?></td>
                        <td><?= $exam['file_path'] ? '✅' : '❌' ?></td>
                        <td>
                            <a href="?delete=<?= $exam['id'] ?>" class="btn btn-sm btn-danger" data-confirm="هل تريد الحذف؟">🗑️</a>
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