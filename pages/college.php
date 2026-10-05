<?php
require_once '../includes/header.php';

$level = $_GET['level'] ?? '';
$subject = $_GET['subject'] ?? '';

$where = "WHERE c.slug = 'college'";
$params = [];

if ($level) { $where .= " AND e.title LIKE ?"; $params[] = "%$level%"; }
if ($subject) { $where .= " AND e.title LIKE ?"; $params[] = "%$subject%"; }

$stmt = $pdo->prepare("SELECT e.*, c.name as category_name FROM exams e JOIN categories c ON e.category_id = c.id $where ORDER BY e.created_at DESC");
$stmt->execute($params);
$exams = $stmt->fetchAll();
?>

<div class="page-header">
    <div class="container">
        <h1>📚 المستوى الإعدادي</h1>
        <p>دروس و امتحانات من الأولى إلى الثالثة إعدادي</p>
        <div class="breadcrumb">
            <a href="/Tafawoq/index.php">الرئيسية</a> / <span>الإعدادي</span>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">

        <form class="filters-bar" method="GET">
            <select name="level" class="filter-select">
                <option value="">كل المستويات</option>
                <option value="الأولى" <?= $level == 'الأولى' ? 'selected' : '' ?>>الأولى إعدادي</option>
                <option value="الثانية" <?= $level == 'الثانية' ? 'selected' : '' ?>>الثانية إعدادي</option>
                <option value="الثالثة" <?= $level == 'الثالثة' ? 'selected' : '' ?>>الثالثة إعدادي</option>
            </select>

            <select name="subject" class="filter-select">
                <option value="">كل المواد</option>
                <option value="رياضيات" <?= $subject == 'رياضيات' ? 'selected' : '' ?>>رياضيات</option>
                <option value="فيزياء" <?= $subject == 'فيزياء' ? 'selected' : '' ?>>فيزياء و كيمياء</option>
                <option value="علوم الحياة" <?= $subject == 'علوم الحياة' ? 'selected' : '' ?>>علوم الحياة و الأرض</option>
                <option value="فرنسية" <?= $subject == 'فرنسية' ? 'selected' : '' ?>>اللغة الفرنسية</option>
                <option value="عربية" <?= $subject == 'عربية' ? 'selected' : '' ?>>اللغة العربية</option>
                <option value="إنجليزية" <?= $subject == 'إنجليزية' ? 'selected' : '' ?>>اللغة الإنجليزية</option>
                <option value="اجتماعيات" <?= $subject == 'اجتماعيات' ? 'selected' : '' ?>>الاجتماعيات</option>
                <option value="تربية إسلامية" <?= $subject == 'تربية إسلامية' ? 'selected' : '' ?>>التربية الإسلامية</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm">تصفية</button>
            <a href="college.php" class="btn btn-outline btn-sm">إعادة</a>
        </form>

        <div class="exams-grid">
            <?php if (empty($exams)): ?>
                <div class="empty-state" style="grid-column:1/-1;">
                    <p>📭 لا توجد نتائج حالياً. سيتم إضافة المحتوى قريباً!</p>
                </div>
            <?php else:
                foreach ($exams as $exam): ?>
                    <div class="exam-card">
                        <div class="exam-badge"><?= htmlspecialchars($exam['category_name']) ?></div>
                        <h4><?= htmlspecialchars($exam['title']) ?></h4>
                        <div class="exam-meta">
                            <span>📅 <?= $exam['year'] ?></span>
                            <span>⬇️ <?= $exam['downloads'] ?></span>
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