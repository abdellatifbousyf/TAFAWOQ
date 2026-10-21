```php
<?php
/**
 * صفحة امتحانات البكالوريا - منصة تفوق TAFAWOQ
 */

require_once __DIR__ . '/../includes/db.php';

// قراءة الفلاتر بطريقة آمنة
$yearInput = $_GET['year'] ?? '';
$branchInput = $_GET['branch'] ?? '';
$typeInput = $_GET['type'] ?? '';

$year = is_string($yearInput) ? trim($yearInput) : '';
$branch = is_string($branchInput) ? trim($branchInput) : '';
$type = is_string($typeInput) ? trim($typeInput) : '';

// الشعب المسموح بها
$allowedBranches = [
    'علوم فيزيائية',
    'علوم الحياة',
    'علوم رياضية',
    'آداب',
    'اقتصاد',
    'تقني',
];

if (!in_array($branch, $allowedBranches, true)) {
    $branch = '';
}

// أنواع الموارد
$allowedTypes = ['exam', 'correction', 'lesson'];

if (!in_array($type, $allowedTypes, true)) {
    $type = '';
}

// التحقق من السنة
if (
    $year !== '' &&
    (!ctype_digit($year) || (int) $year < 2010 || (int) $year > 2026)
) {
    $year = '';
}

// بناء الاستعلام
$sql = "SELECT e.*, c.name AS category_name
        FROM exams e
        JOIN categories c ON c.id = e.category_id
        WHERE c.slug = ?";

$params = ['bac'];

if ($year !== '') {
    $sql .= " AND e.year = ?";
    $params[] = (int) $year;
}

if ($branch !== '') {
    $sql .= " AND e.title LIKE ?";
    $params[] = '%' . $branch . '%';
}

if ($type !== '') {
    $sql .= " AND e.type = ?";
    $params[] = $type;
}

$sql .= " ORDER BY e.year DESC, e.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$exams = $stmt->fetchAll();

// رابط المشروع المحلي
$base = '/TAFAWOQ';

// تسمية نوع المورد
function bacTypeLabel(?string $examType): string
{
    return match ($examType) {
        'exam' => 'امتحان',
        'correction' => 'تصحيح',
        'lesson' => 'درس',
        default => 'مورد تعليمي',
    };
}

// إنشاء رابط الملف
function bacDownloadUrl(string $baseUrl, ?string $storedPath): ?string
{
    $path = trim((string) $storedPath);

    if ($path === '') {
        return null;
    }

    $path = str_replace('\\', '/', $path);
    $path = ltrim($path, '/');

    if (str_starts_with($path, 'uploads/')) {
        $path = substr($path, strlen('uploads/'));
    }

    $parts = explode('/', $path);

    foreach ($parts as $part) {
        if ($part === '' || $part === '.' || $part === '..') {
            return null;
        }
    }

    $encodedPath = implode('/', array_map('rawurlencode', $parts));

    return rtrim($baseUrl, '/') . '/uploads/' . $encodedPath;
}

require_once __DIR__ . '/../includes/header.php';
?>

<!-- عنوان الصفحة: نفس الهيكل المستعمل في college.php -->
<div class="page-header">
    <div class="container">
        <h1>🎓 امتحانات البكالوريا</h1>

        <p>
            استعد للامتحانات الوطنية والجهوية،
            واكتشف الامتحانات والتصحيحات لجميع الشعب.
        </p>

        <div class="breadcrumb">
            <a href="<?= htmlspecialchars($base . '/index.php', ENT_QUOTES, 'UTF-8') ?>">
                الرئيسية
            </a>
            <span aria-hidden="true">/</span>
            <span>البكالوريا</span>
        </div>
    </div>
</div>

<!-- المحتوى الرئيسي -->
<section class="section">
    <div class="container">

        <!-- فلاتر البحث بنفس أصناف التصميم المشتركة -->
        <form
            class="filters-bar"
            method="GET"
            action="<?= htmlspecialchars($base . '/pages/bac.php', ENT_QUOTES, 'UTF-8') ?>"
        >

            <select name="year" class="filter-select" aria-label="السنة">
                <option value="">كل السنوات</option>

                <?php for ($y = 2026; $y >= 2010; $y--): ?>
                    <option
                        value="<?= $y ?>"
                        <?= $year === (string) $y ? 'selected' : '' ?>
                    >
                        <?= $y ?>
                    </option>
                <?php endfor; ?>
            </select>

            <select name="branch" class="filter-select" aria-label="الشعبة">
                <option value="">كل الشعب</option>

                <option value="علوم فيزيائية"
                    <?= $branch === 'علوم فيزيائية' ? 'selected' : '' ?>>
                    العلوم الفيزيائية
                </option>

                <option value="علوم الحياة"
                    <?= $branch === 'علوم الحياة' ? 'selected' : '' ?>>
                    علوم الحياة والأرض
                </option>

                <option value="علوم رياضية"
                    <?= $branch === 'علوم رياضية' ? 'selected' : '' ?>>
                    العلوم الرياضية
                </option>

                <option value="آداب"
                    <?= $branch === 'آداب' ? 'selected' : '' ?>>
                    الآداب والعلوم الإنسانية
                </option>

                <option value="اقتصاد"
                    <?= $branch === 'اقتصاد' ? 'selected' : '' ?>>
                    العلوم الاقتصادية والتدبير
                </option>

                <option value="تقني"
                    <?= $branch === 'تقني' ? 'selected' : '' ?>>
                    العلوم والتقنيات
                </option>
            </select>

            <select name="type" class="filter-select" aria-label="نوع المورد">
                <option value="">جميع الأنواع</option>

                <option value="exam"
                    <?= $type === 'exam' ? 'selected' : '' ?>>
                    الامتحانات
                </option>

                <option value="correction"
                    <?= $type === 'correction' ? 'selected' : '' ?>>
                    التصحيحات
                </option>

                <option value="lesson"
                    <?= $type === 'lesson' ? 'selected' : '' ?>>
                    الدروس
                </option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm">
                🔍 تصفية
            </button>

            <a
                href="<?= htmlspecialchars($base . '/pages/bac.php', ENT_QUOTES, 'UTF-8') ?>"
                class="btn btn-outline btn-sm"
            >
                إعادة
            </a>
        </form>

        <!-- عدد النتائج -->
        <div class="section-heading">
            <h2>📚 مكتبة امتحانات البكالوريا</h2>
            <p>
                عدد الموارد المتوفرة:
                <strong><?= count($exams) ?></strong>
            </p>
        </div>

        <!-- بطاقات النتائج بنفس هيكل college.php -->
        <div class="exams-grid">

            <?php if (empty($exams)): ?>

                <div class="empty-state" style="grid-column: 1 / -1;">
                    <h3>📭 لا توجد نتائج حالياً</h3>
                    <p>
                        جرب تغيير السنة أو الشعبة أو نوع المورد.
                    </p>
                    <a
                        href="<?= htmlspecialchars($base . '/pages/bac.php', ENT_QUOTES, 'UTF-8') ?>"
                        class="btn btn-primary"
                    >
                        عرض جميع الامتحانات
                    </a>
                </div>

            <?php else: ?>

                <?php foreach ($exams as $exam): ?>

                    <article class="exam-card">

                        <div class="exam-badge">
                            <?= htmlspecialchars(
                                (string) ($exam['year'] ?? 'غير محددة'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                        <h4>
                            <?= htmlspecialchars(
                                (string) ($exam['title'] ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h4>

                        <div class="exam-meta">
                            <span>
                                📂
                                <?= htmlspecialchars(
                                    bacTypeLabel($exam['type'] ?? null),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>

                            <span>
                                ⬇️
                                <?= (int) ($exam['downloads'] ?? 0) ?>
                                تحميل
                            </span>
                        </div>

                        <?php
                        $downloadUrl = bacDownloadUrl(
                            $base,
                            $exam['file_path'] ?? null
                        );
                        ?>

                        <?php if ($downloadUrl !== null): ?>
                            <a
                                href="<?= htmlspecialchars(
                                    $downloadUrl,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                class="btn btn-sm btn-download"
                                download
                            >
                                📥 تحميل الملف
                            </a>
                        <?php endif; ?>

                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
```