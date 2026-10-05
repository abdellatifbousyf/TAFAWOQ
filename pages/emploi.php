<?php
require_once '../includes/header.php';

$sector = $_GET['sector'] ?? '';

$where = "WHERE c.slug = 'emploi'";
$params = [];

if ($sector) { $where .= " AND e.title LIKE ?"; $params[] = "%$sector%"; }

$stmt = $pdo->prepare("SELECT e.*, c.name as category_name FROM exams e JOIN categories c ON e.category_id = c.id $where ORDER BY e.created_at DESC");
$stmt->execute($params);
$exams = $stmt->fetchAll();
?>

<div class="page-header">
    <div class="container">
        <h1>💼 التوظيف و المباريات</h1>
        <p>أحدث مباريات التوظيف في القطاع العام و عروض الشغل</p>
        <div class="breadcrumb">
            <a href="/Tafawoq/index.php">الرئيسية</a> / <span>التوظيف</span>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">

        <div class="features-grid" style="margin-bottom: 40px;">
            <div class="feature">
                <span class="feature-icon">🏛️</span>
                <h4>القطاع العام</h4>
                <p>مباريات الوزارات و الجماعات المحلية</p>
            </div>
            <div class="feature">
                <span class="feature-icon">🏢</span>
                <h4>القطاع الخاص</h4>
                <p>عروض الشغل في الشركات الكبرى</p>
            </div>
            <div class="feature">
                <span class="feature-icon">🎖️</span>
                <h4>القوات المسلحة</h4>
                <p>مباريات الدرك الملكي و القوات المساعدة</p>
            </div>
        </div>

        <form class="filters-bar" method="GET">
            <select name="sector" class="filter-select">
                <option value="">كل القطاعات</option>
                <option value="تعليم" <?= $sector == 'تعليم' ? 'selected' : '' ?>>التعليم</option>
                <option value="صحة" <?= $sector == 'صحة' ? 'selected' : '' ?>>الصحة</option>
                <option value="أمن" <?= $sector == 'أمن' ? 'selected' : '' ?>>الأمن و الدرك</option>
                <option value="جماعات" <?= $sector == 'جماعات' ? 'selected' : '' ?>>الجماعات المحلية</option>
                <option value="بنوك" <?= $sector == 'بنوك' ? 'selected' : '' ?>>البنوك و المالية</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">تصفية</button>
        </form>

        <div class="exams-grid">
            <?php if (empty($exams)): ?>
                <div class="empty-state" style="grid-column:1/-1;">
                    <p>📭 لا توجد عروض توظيف حالياً. تابعنا يومياً!</p>
                </div>
            <?php else:
                foreach ($exams as $exam): ?>
                    <div class="exam-card">
                        <div class="exam-badge" style="background:#fff3e0;color:#e65100;">مباراة توظيف</div>
                        <h4><?= htmlspecialchars($exam['title']) ?></h4>
                        <div class="exam-meta">
                            <span>📅 <?= $exam['year'] ?></span>
                            <span>⬇️ <?= $exam['downloads'] ?></span>
                        </div>
                        <?php if ($exam['file_path']): ?>
                            <a href="/Tafawoq/uploads/<?= $exam['file_path'] ?>" class="btn btn-sm btn-download" download>📥 تحميل الإعلان</a>
                        <?php endif; ?>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>

    </div>
</section>

<?php require_once '../includes/footer.php'; ?>