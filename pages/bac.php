<?php
require_once '../includes/header.php';

// فلترة
$year = $_GET['year'] ?? '';
$branch = $_GET['branch'] ?? '';
$type = $_GET['type'] ?? '';

$where = "WHERE c.slug = 'bac'";
$params = [];

if ($year) { $where .= " AND e.year = ?"; $params[] = $year; }
if ($branch) { $where .= " AND e.title LIKE ?"; $params[] = "%$branch%"; }
if ($type) { $where .= " AND e.type = ?"; $params[] = $type; }

$stmt = $pdo->prepare("SELECT e.*, c.name as category_name FROM exams e JOIN categories c ON e.category_id = c.id $where ORDER BY e.year DESC, e.created_at DESC");
$stmt->execute($params);
$exams = $stmt->fetchAll();
?>

<div class="page-header">
    <div class="container">
        <h1>🎓 امتحانات البكالوريا</h1>
        <p>امتحانات وطنية وجهوية مع التصحيح لجميع الشعب و السنوات</p>
        <div class="breadcrumb">
            <a href="/Tafawoq/index.php">الرئيسية</a> / <span>البكالوريا</span>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">

        <!-- فلاتر -->
        <form class="filters-bar" method="GET">
            <select name="year" class="filter-select">
                <option value="">كل السنوات</option>
                <?php for ($y = 2026; $y >= 2010; $y--): ?>
                    <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>

            <select name="branch" class="filter-select">
                <option value="">كل الشعب</option>
                <option value="علوم فيزيائية" <?= $branch == 'علوم فيزيائية' ? 'selected' : '' ?>>علوم فيزيائية</option>
                <option value="علوم الحياة" <?= $branch == 'علوم الحياة' ? 'selected' : '' ?>>علوم الحياة و الأرض</option>
                <option value="علوم رياضية" <?= $branch == 'علوم رياضية' ? 'selected' : '' ?>>علوم رياضية</option>
                <option value="آداب" <?= $branch == 'آداب' ? 'selected' : '' ?>>آداب و علوم إنسانية</option>
                <option value="اقتصاد" <?= $branch == 'اقتصاد' ? 'selected' : '' ?>>علوم اقتصادية و تدبير</option>
                <option value="تقني" <?= $branch == 'تقني' ? 'selected' : '' ?>>علوم و تقنيات كهربائية / ميكانيكية</option>
            </select>

            <select name="type" class="filter-select">
                <option value="">الكل</option>
                <option value="exam" <?= $type == 'exam' ? 'selected' : '' ?>>امتحانات</option>
                <option value="correction" <?= $type == 'correction' ? 'selected' : '' ?>>تصحيح</option>
                <option value="lesson" <?= $type == 'lesson' ? 'selected' : '' ?>>دروس</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm">تصفية</button>
            <a href="bac.php" class="btn btn-outline btn-sm">إعادة</a>
        </form>

        <!-- النتائج -->
        <div class="exams-grid">
            <?php if (empty($exams)): ?>
                <div class="empty-state" style="grid-column:1/-1;">
                    <p>📭 لا توجد نتائج. جرّب تغيير الفلاتر أو عد لاحقاً.</p>
                </div>
            <?php else:
                foreach ($exams as $exam): ?>
                    <div class="exam-card">
                        <div class="exam-badge"><?= $exam['year'] ?></div>
                        <h4><?= htmlspecialchars($exam['title']) ?></h4>
                        <div class="exam-meta">
                            <span>📂 <?= $exam['type'] == 'exam' ? 'امتحان' : ($exam['type'] == 'correction' ? 'تصحيح' : 'درس') ?></span>
                            <span>⬇️ <?= $exam['downloads'] ?></span>
                        </div>
                        <?php if ($exam['file_path']): ?>
                            <a href="/Tafawoq/uploads/<?= $exam['file_path'] ?>" class="btn btn-sm btn-download" download>📥 تحميل PDF</a>
                        <?php endif; ?>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>

    </div>
</section>

<?php require_once '../includes/footer.php'; ?>