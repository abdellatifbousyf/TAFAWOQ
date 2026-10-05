<?php
require_once '../includes/header.php';

$domain = $_GET['domain'] ?? '';

$where = "WHERE c.slug = 'formation'";
$params = [];

if ($domain) { $where .= " AND e.title LIKE ?"; $params[] = "%$domain%"; }

$stmt = $pdo->prepare("SELECT e.*, c.name as category_name FROM exams e JOIN categories c ON e.category_id = c.id $where ORDER BY e.created_at DESC");
$stmt->execute($params);
$exams = $stmt->fetchAll();
?>

<div class="page-header">
    <div class="container">
        <h1>🔧 التكوين المهني</h1>
        <p>تكوينات OFPPT و شهادات مهنية معترف بها في مختلف التخصصات</p>
        <div class="breadcrumb">
            <a href="/Tafawoq/index.php">الرئيسية</a> / <span>التكوين المهني</span>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">

        <!-- معلومات التكوين -->
        <div class="features-grid" style="margin-bottom: 40px;">
            <div class="feature">
                <span class="feature-icon">🏫</span>
                <h4>OFPPT</h4>
                <p>مكتب التكوين المهني و إنعاش الشغل</p>
            </div>
            <div class="feature">
                <span class="feature-icon">📜</span>
                <h4>شهادات معترف بها</h4>
                <p>دبلوم التقني المتخصص، التقني، التأهيل</p>
            </div>
            <div class="feature">
                <span class="feature-icon">💻</span>
                <h4>تخصصات مطلوبة</h4>
                <p>تطوير الويب، المحاسبة، التسيير، الصناعة...</p>
            </div>
        </div>

        <form class="filters-bar" method="GET">
            <select name="domain" class="filter-select">
                <option value="">كل التخصصات</option>
                <option value="تطوير" <?= $domain == 'تطوير' ? 'selected' : '' ?>>تطوير رقمي</option>
                <option value="محاسبة" <?= $domain == 'محاسبة' ? 'selected' : '' ?>>محاسبة و تدبير</option>
                <option value="كهرباء" <?= $domain == 'كهرباء' ? 'selected' : '' ?>>كهرباء و صيانة</option>
                <option value="سياحة" <?= $domain == 'سياحة' ? 'selected' : '' ?>>سياحة و فندقية</option>
                <option value="صحة" <?= $domain == 'صحة' ? 'selected' : '' ?>>صحة و تمريض</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">تصفية</button>
        </form>

        <div class="exams-grid">
            <?php if (empty($exams)): ?>
                <div class="empty-state" style="grid-column:1/-1;">
                    <p>📭 لا توجد تكوينات حالياً. سيتم الإضافة قريباً!</p>
                </div>
            <?php else:
                foreach ($exams as $exam): ?>
                    <div class="exam-card">
                        <div class="exam-badge">تكوين مهني</div>
                        <h4><?= htmlspecialchars($exam['title']) ?></h4>
                        <div class="exam-meta">
                            <span>📅 <?= $exam['year'] ?></span>
                        </div>
                        <?php if ($exam['file_path']): ?>
                            <a href="/Tafawoq/uploads/<?= $exam['file_path'] ?>" class="btn btn-sm btn-download" download>📥 تحميل</a>
                        <?php endif; ?>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>

    </div>
</section>

<?php require_once '../includes/footer.php'; ?>