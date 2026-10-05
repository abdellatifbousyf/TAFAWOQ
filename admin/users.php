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
    if ($id != $_SESSION['admin']) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        $message = 'تم حذف المستخدم.';
    } else {
        $message = 'لا يمكنك حذف حسابك!';
    }
}

// إضافة مستخدم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (strlen($password) < 6) {
        $message = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $message = 'البريد الإلكتروني مستخدم بالفعل.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hash, $role]);
            $message = 'تم إضافة المستخدم بنجاح!';
        }
    }
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين - تفوق</title>
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
            <li><a href="categories.php">📂 الأقسام</a></li>
            <li><a href="users.php" class="active">👥 المستخدمين</a></li>
            <li><a href="/Tafawoq/index.php">🌐 عرض الموقع</a></li>
            <li><a href="login.php?logout=1" style="color:#ff6b6b;">🚪 خروج</a></li>
        </ul>
    </aside>

    <main class="admin-content">
        <h2>👥 إدارة المستخدمين</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>

        <div class="admin-form" style="margin-bottom:30px;">
            <h3 style="margin-bottom:15px;">إضافة مستخدم جديد</h3>
            <form method="POST">
                <div class="form-group">
                    <label>الاسم *</label>
                    <input type="text" name="username" required placeholder="الاسم الكامل">
                </div>
                <div class="form-group">
                    <label>البريد الإلكتروني *</label>
                    <input type="email" name="email" required placeholder="example@email.com" dir="ltr">
                </div>
                <div class="form-group">
                    <label>كلمة المرور *</label>
                    <input type="password" name="password" required minlength="6" placeholder="6 أحرف على الأقل">
                </div>
                <div class="form-group">
                    <label>الدور</label>
                    <select name="role">
                        <option value="user">مستخدم عادي</option>
                        <option value="admin">مدير</option>
                    </select>
                </div>
                <button type="submit" name="add_user" class="btn btn-success">➕ إضافة</button>
            </form>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>البريد</th>
                    <th>الدور</th>
                    <th>تاريخ التسجيل</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td dir="ltr"><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span style="padding:3px 10px;border-radius:20px;font-size:0.8rem;font-weight:600;
                                background:<?= $user['role'] == 'admin' ? '#ffe0e0' : '#e0f0ff' ?>;
                                color:<?= $user['role'] == 'admin' ? '#c00' : '#0066cc' ?>;">
                                <?= $user['role'] == 'admin' ? 'مدير' : 'مستخدم' ?>
                            </span>
                        </td>
                        <td><?= date('Y/m/d', strtotime($user['created_at'])) ?></td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['admin']): ?>
                                <a href="?delete=<?= $user['id'] ?>" class="btn btn-sm btn-danger" data-confirm="حذف المستخدم؟">🗑️</a>
                            <?php else: ?>
                                <span style="color:var(--gray-500);font-size:0.85rem;">أنت</span>
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