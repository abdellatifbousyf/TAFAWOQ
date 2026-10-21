<?php
/**
 * صفحة امتحانات البكالوريا لمنصة تفوّق.
 * تعرض الامتحانات المسجلة في قاعدة البيانات، وتتيح تصفيتها حسب السنة والشعبة والنوع.
 */

// تحميل اتصال قاعدة البيانات قبل تنفيذ الاستعلامات، دون إخراج HTML إلى المتصفح بعد.
require_once __DIR__ . '/../includes/db.php';

// قراءة قيم الفلاتر من عنوان الصفحة، مع التأكد من أنها نصوص وليست مصفوفات.
$yearInput = $_GET['year'] ?? '';
$branchInput = $_GET['branch'] ?? '';
$typeInput = $_GET['type'] ?? '';

$year = is_string($yearInput) ? trim($yearInput) : '';
$branch = is_string($branchInput) ? trim($branchInput) : '';
$type = is_string($typeInput) ? trim($typeInput) : '';

// تحديد الشعب المسموح بها حتى لا تُستخدم قيم غير موجودة في قائمة الاختيارات.
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

// تحديد أنواع الموارد المسموح بها وفق القيم المعرفة في قاعدة البيانات.
$allowedTypes = ['exam', 'correction', 'lesson'];
if (!in_array($type, $allowedTypes, true)) {
    $type = '';
}

// قبول السنة إذا كانت رقمًا ضمن النطاق المعروض في نموذج التصفية.
if ($year !== '' && (!ctype_digit($year) || (int) $year < 2010 || (int) $year > 2026)) {
    $year = '';
}

// بناء استعلام آمن يعرض موارد قسم البكالوريا فقط.
// تُضاف شروط التصفية عند اختيار المستخدم لقيمة، وتُمرر القيم عبر Prepared Statements.
$sql = "SELECT e.*, c.name AS category_name
        FROM exams e
        JOIN categories c ON c.id = e.category_id
        WHERE c.slug = ?";
$params = ['bac'];

if ($year !== '') {
    $sql .= ' AND e.year = ?';
    $params[] = (int) $year;
}

if ($branch !== '') {
    $sql .= ' AND e.title LIKE ?';
    $params[] = '%' . $branch . '%';
}

if ($type !== '') {
    $sql .= ' AND e.type = ?';
    $params[] = $type;
}

// ترتيب النتائج حسب السنة من الأحدث إلى الأقدم، ثم حسب تاريخ الإضافة.
$sql .= ' ORDER BY e.year DESC, e.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$exams = $stmt->fetchAll();

// تحديد رابط أساسي احتياطي إن لم يكن المتغير قد عُرّف في الترويسة المشتركة.
$base = '/TAFAWOQ';

// تحويل نوع المورد إلى تسمية عربية واضحة للزائر.
function bacTypeLabel(?string $examType): string
{
    return match ($examType) {
        'exam' => 'امتحان',
        'correction' => 'تصحيح',
        'lesson' => 'درس',
        default => 'مورد تعليمي',
    };
}

// إنشاء رابط تحميل نسبي إلى مجلد uploads مع ترميز كل جزء من المسار.
// القيم المحفوظة في المشروع تكون عادة مثل exams/اسم_الملف.pdf.
function bacDownloadUrl(string $baseUrl, ?string $storedPath): ?string
{
    $path = trim((string) $storedPath);
    if ($path === '') {
        return null;
    }

    // توحيد فواصل المسار ومنع الصعود إلى مجلدات خارج مجلد الرفع.
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

// تحميل الترويسة المشتركة بعد تجهيز البيانات حتى لا يبدأ إخراج الصفحة قبل معالجة الطلب.
require_once __DIR__ . '/../includes/header.php';
?>

<!-- قسم عنوان صفحة البكالوريا، مع مسار التنقل لتسهيل الرجوع إلى الصفحة الرئيسية. -->
<div class="page-header">
    <div class="container">
        <h1>🎓 امتحانات البكالوريا</h1>
        <p>امتحانات وطنية وجهوية مع التصحيح لجميع الشعب والسنوات.</p>
        <div class="breadcrumb">
            <a href="<?= htmlspecialchars($base . '/index.php', ENT_QUOTES, 'UTF-8') ?>">الرئيسية</a>
            <span aria-hidden="true">/</span>
            <span>البكالوريا</span>
        </div>
    </div>
</div>

<!-- قسم التصفية وقائمة الامتحانات. -->
<section class="section">
    <div class="container">

        <!-- يرسل نموذج التصفية الاختيارات عبر GET حتى تظهر في عنوان الصفحة. -->
        <form
            class="filters-bar"
            method="GET"
            action="<?= htmlspecialchars($base . '/pages/bac.php', ENT_QUOTES, 'UTF-8') ?>"
        >
            <!-- تصفية الموارد بحسب السنة. -->
            <select name="year" class="filter-select" aria-label="تصفية حسب السنة">
                <option value="">كل السنوات</option>
                <?php for ($y = 2026; $y >= 2010; $y--): ?>
                    <option value="<?= $y ?>" <?= $year === (string) $y ? 'selected' : '' ?>>
                        <?= $y ?>
                    </option>
                <?php endfor; ?>
            </select>

            <!-- تصفية الموارد بحسب الشعبة المذكورة في عنوان المورد. -->
            <select name="branch" class="filter-select" aria-label="تصفية حسب الشعبة">
                <option value="">كل الشعب</option>
                <option value="علوم فيزيائية" <?= $branch === 'علوم فيزيائية' ? 'selected' : '' ?>>علوم فيزيائية</option>
                <option value="علوم الحياة" <?= $branch === 'علوم الحياة' ? 'selected' : '' ?>>علوم الحياة والأرض</option>
                <option value="علوم رياضية" <?= $branch === 'علوم رياضية' ? 'selected' : '' ?>>علوم رياضية</option>
                <option value="آداب" <?= $branch === 'آداب' ? 'selected' : '' ?>>الآداب والعلوم الإنسانية</option>
                <option value="اقتصاد" <?= $branch === 'اقتصاد' ? 'selected' : '' ?>>العلوم الاقتصادية والتدبير</option>
                <option value="تقني" <?= $branch === 'تقني' ? 'selected' : '' ?>>العلوم والتقنيات الكهربائية / الميكانيكية</option>
            </select>

            <!-- تصفية الموارد بحسب كونها امتحانًا أو تصحيحًا أو درسًا. -->
            <select name="type" class="filter-select" aria-label="تصفية حسب نوع المورد">
                <option value="">جميع الأنواع</option>
                <option value="exam" <?= $type === 'exam' ? 'selected' : '' ?>>الامتحانات</option>
                <option value="correction" <?= $type === 'correction' ? 'selected' : '' ?>>التصحيحات</option>
                <option value="lesson" <?= $type === 'lesson' ? 'selected' : '' ?>>الدروس</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm">تصفية</button>
            <!-- يمسح رابط إعادة التعيين جميع الفلاتر المختارة. -->
            <a href="<?= htmlspecialchars($base . '/pages/bac.php', ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline btn-sm">إعادة</a>
        </form>

        <!-- عرض النتائج في بطاقات، أو إظهار رسالة عند عدم وجود نتائج. -->
        <div class="exams-grid">
            <?php if (empty($exams)): ?>
                <div class="empty-state" style="grid-column: 1 / -1;">
                    <p>📭 لا توجد نتائج مطابقة. جرّب تغيير الفلاتر أو العودة لاحقًا.</p>
                </div>
            <?php else: ?>
                <?php foreach ($exams as $exam): ?>
                    <article class="exam-card">
                        <!-- عرض السنة في شارة أعلى البطاقة. -->
                        <div class="exam-badge">
                            <?= htmlspecialchars((string) ($exam['year'] ?? 'غير محددة'), ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <!-- تعقيم النصوص قبل عرضها لحماية الصفحة من إدخال HTML غير مرغوب فيه. -->
                        <h4><?= htmlspecialchars((string) ($exam['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h4>

                        <div class="exam-meta">
                            <span>📂 <?= htmlspecialchars(bacTypeLabel($exam['type'] ?? null), ENT_QUOTES, 'UTF-8') ?></span>
                            <span>⬇️ <?= (int) ($exam['downloads'] ?? 0) ?> تحميل</span>
                        </div>

                        <?php $downloadUrl = bacDownloadUrl($base, $exam['file_path'] ?? null); ?>
                        <?php if ($downloadUrl !== null): ?>
                            <!-- لا يظهر زر التحميل إذا لم يكن للمورد مسار ملف صالح. -->
                            <a
                                href="<?= htmlspecialchars($downloadUrl, ENT_QUOTES, 'UTF-8') ?>"
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

<!-- تحميل التذييل المشترك الذي يحتوي على روابط الموقع ونهاية بنية الصفحة. -->
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
