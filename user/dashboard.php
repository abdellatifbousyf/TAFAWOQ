<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user'];

// تحميلات المستخدم
$stmt = $pdo->prepare("
    SELECT e.*, c.name as category_name, ud.downloaded_at
    FROM user_downloads ud
    JOIN exams e ON ud.exam_id = e.id
    LEFT JOIN categories c ON e.category_id = c.id
    WHERE ud.user_id = ?
    ORDER BY ud.downloaded_at DESC
");
$stmt->execute([$user_id]);
$downloads = $stmt->fetchAll();

// إحصائيات
$totalDownloads = count($downloads);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حسابي - تفوق</title>
    <link rel="stylesheet" href="/Tafawoq/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
</head>
<body>

<?php require_once '../includes/header.php'; ?>

<section class="section">
    <div class="container">
        <div class="user-dashboard">
            <div class="user-header">
                <div class="user-info">
                    <div class="user-avatar"></div>
                    <div>
                        <h2>مرحباً، <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</h2>
                        <p>إدارة حسابك و تحميلاتك</p>
                    </div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">⬇️</div>
                    <div class="stat-value"><?= $totalDownloads ?></div>
                    <div class="stat-title">تحميل</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-value"><?= date('Y') ?></div>
                    <div class="stat-title">سنة التسجيل</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">👤</div>
                    <div class="stat-value">مستخدم</div>
                    <div class="stat-title">نوع الحساب</div>
                </div>
            </div>

            <h3 style="margin: 30px 0 20px;"> التحميلات الأخيرة</h3>

            <?php if (empty($downloads)): ?>
                <div class="empty-state">
                    <p>📭 لم تقم بأي تحميل بعد.</p>
                    <a href="/Tafawoq/index.php" class="btn btn-primary" style="margin-top: 15px;">تصفح الامتحانات</a>
                </div>
            <?php else: ?>
                <div class="exams-grid">
                    <?php foreach ($downloads as $exam): ?>
                        <div class="exam-card">
                            <div class="exam-badge"><?= htmlspecialchars($exam['category_name'] ?? 'عام') ?></div>
                            <h4><?= htmlspecialchars($exam['title']) ?></h4>
                            <div class="exam-meta">
                                <span>📅 <?= $exam['year'] ?></span>
                                <span>⬇️ <?= date('Y/m/d', strtotime($exam['downloaded_at'])) ?></span>
                            </div>
                            <?php if ($exam['file_path']): ?>
                                <a href="/Tafawoq/uploads/<?= $exam['file_path'] ?>" class="btn btn-sm btn-download" download>📥 تحميل</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>