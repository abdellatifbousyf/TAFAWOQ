<?php
/**
 * صفحة السلك الإعدادي في منصة تفوّق.
 * تعرض الامتحانات المسجلة في قاعدة البيانات، وتتيح تصفيتها حسب المستوى والمادة.
 */

// تشغيل الجلسة حتى تستطيع واجهة الموقع معرفة حالة تسجيل دخول المستخدم.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تضمين الترويسة المشتركة؛ وهي تتولى أيضاً إعداد الاتصال بقاعدة البيانات.
require_once __DIR__ . '/../includes/header.php';

// قراءة قيم التصفية من عنوان الصفحة مع إزالة المسافات الزائدة.
$level = trim((string) ($_GET['level'] ?? ''));
$subject = trim((string) ($_GET['subject'] ?? ''));

// قصر المستوى على الخيارات المعروضة في النموذج حتى لا نعالج قيماً غير معروفة.
$allowedLevels = ['الأولى', 'الثانية', 'الثالثة'];
if (!in_array($level, $allowedLevels, true)) {
    $level = '';
}

// قصر المادة على قائمة المواد المتاحة في النموذج.
$allowedSubjects = [
    'رياضيات',
    'فيزياء',
    'علوم الحياة',
    'فرنسية',
    'عربية',
    'إنجليزية',
    'اجتماعيات',
    'تربية إسلامية',
];
if (!in_array($subject, $allowedSubjects, true)) {
    $subject = '';
}

// إنشاء الاستعلام الأساسي لعرض عناصر قسم الإعدادي فقط.
$sql = "SELECT e.*, c.name AS category_name
        FROM exams AS e
        INNER JOIN categories AS c ON c.id = e.category_id
        WHERE c.slug = ?";
$params = ['college'];

// إضافة شرط المستوى عند اختياره، مع تمرير القيمة كمعامل آمن.
if ($level !== '') {
    $sql .= " AND e.title LIKE ?";
    $params[] = '%' . $level . '%';
}

// إضافة شرط المادة عند اختيارها، دون دمج مدخلات المستخدم مباشرة في SQL.
if ($subject !== '') {
    $sql .= " AND e.title LIKE ?";
    $params[] = '%' . $subject . '%';
}

// ترتيب النتائج من الأحدث إلى الأقدم.
$sql .= " ORDER BY e.created_at DESC, e.year DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$exams = $stmt->fetchAll();

// احتساب الإحصائيات من قاعدة البيانات بدلاً من إظهار أرقام ثابتة.
$totalExams = (int) $pdo->query("SELECT COUNT(*) FROM exams")->fetchColumn();
$totalLessons = (int) $pdo->query("SELECT COUNT(*) FROM exams WHERE type = 'lesson'")->fetchColumn();
$totalUsers = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// تجهيز مسار آمن لرابط الملف المحفوظ في سجل الامتحان.
$makeDownloadUrl = static function (?string $storedPath) use ($base): ?string {
    if ($storedPath === null || trim($storedPath) === '') {
        return null;
    }

    // توحيد فواصل المسار ومنع الانتقال إلى مجلدات خارج مجلد الرفع.
    $path = str_replace('\\', '/', trim($storedPath));
    $rawSegments = explode('/', $path);

    // رفض المسار إذا احتوى على محاولة انتقال إلى مجلد أب.
    if (in_array('..', $rawSegments, true)) {
        return null;
    }

    $segments = array_values(array_filter($rawSegments, static function ($part) {
        return $part !== '' && $part !== '.';
    }));

    if ($segments === []) {
        return null;
    }

    // ترميز كل جزء من المسار حتى لا تؤثر المسافات أو الأحرف الخاصة في الرابط.
    $encodedPath = implode('/', array_map('rawurlencode', $segments));
    return rtrim($base, '/') . '/uploads/' . $encodedPath;
};
?>

<style>
/* تنسيق الصفحة محصور داخل college-page حتى لا يؤثر في بقية صفحات الموقع. */
.college-page {
    --college-primary: #6366f1;
    --college-primary-dark: #4f46e5;
    --college-deep: #3730a3;
    --college-pink: #ec4899;
    --college-purple: #8b5cf6;
    --college-dark: #0f172a;
    --college-muted: #64748b;
    background: #f8fafc;
    color: var(--college-dark);
    font-family: 'Cairo', sans-serif;
    overflow: hidden;
}

.college-page *,
.college-page *::before,
.college-page *::after { box-sizing: border-box; }

.college-page .container {
    width: 100%;
    max-width: 1200px;
    margin-inline: auto;
    padding-inline: 20px;
}

/* رأس الصفحة ذو الخلفية المتدرجة والمؤثرات الضوئية. */
.college-page .college-hero {
    position: relative;
    overflow: hidden;
    padding: 78px 0 96px;
    color: #fff;
    background: linear-gradient(135deg, #0b1024 0%, #1e1b4b 55%, #312e81 100%);
    isolation: isolate;
}

.college-page .college-hero::before,
.college-page .college-hero::after {
    position: absolute;
    z-index: -1;
    content: '';
    border-radius: 50%;
    filter: blur(85px);
    opacity: .48;
    pointer-events: none;
    animation: collegeBlob 12s ease-in-out infinite alternate;
}

.college-page .college-hero::before {
    top: -140px;
    left: -90px;
    width: 410px;
    height: 410px;
    background: var(--college-primary);
}

.college-page .college-hero::after {
    right: -70px;
    bottom: -150px;
    width: 360px;
    height: 360px;
    background: var(--college-pink);
    animation-delay: -4s;
}

@keyframes collegeBlob {
    from { transform: translate3d(0, 0, 0) scale(1); }
    to { transform: translate3d(26px, 32px, 0) scale(1.08); }
}

.college-page .college-hero-grid {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
    align-items: center;
    gap: 28px;
}

.college-page .hero-copy { min-width: 0; }

.college-page .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 22px;
    padding: 9px 17px;
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 999px;
    background: rgba(255,255,255,.08);
    color: #e0e7ff;
    font-weight: 700;
    font-size: .92rem;
    backdrop-filter: blur(10px);
}

.college-page .college-hero h1 {
    margin: 0 0 14px;
    color: #fff;
    font-size: clamp(2rem, 4vw, 3.2rem);
    font-weight: 900;
    line-height: 1.3;
    text-shadow: 0 5px 22px rgba(0,0,0,.28);
}

.college-page .college-hero p {
    max-width: 610px;
    margin: 0 0 22px;
    color: #cbd5e1;
    font-size: 1.08rem;
    line-height: 1.9;
}

.college-page .breadcrumb {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 9px;
    padding: 9px 16px;
    border: 1px solid rgba(255,255,255,.16);
    border-radius: 999px;
    background: rgba(255,255,255,.07);
    color: #c7d2fe;
    font-size: .9rem;
}

.college-page .breadcrumb a {
    color: #fff;
    font-weight: 700;
    text-decoration: none;
}

.college-page .breadcrumb a:hover { color: #fbcfe8; }

/* مساحة المجسم الثلاثي الأبعاد؛ تظهر صورة رمزية عند تعذر تشغيل WebGL. */
.college-page .head-stage {
    position: relative;
    min-width: 0;
    height: 330px;
}

.college-page .head-stage::before {
    position: absolute;
    right: 10%;
    bottom: 5%;
    left: 10%;
    height: 72px;
    content: '';
    background: radial-gradient(ellipse at center, rgba(129,140,248,.45), transparent 70%);
    filter: blur(12px);
}

.college-page #head-canvas {
    position: absolute;
    inset: 0;
    display: block;
    width: 100%;
    height: 100%;
    cursor: grab;
    touch-action: pan-y;
}

.college-page #head-canvas:active { cursor: grabbing; }

.college-page .stage-fallback {
    position: absolute;
    inset: 0;
    display: none;
    place-items: center;
    font-size: clamp(5rem, 12vw, 8rem);
    filter: drop-shadow(0 24px 24px rgba(0,0,0,.35));
    animation: collegeBob 5s ease-in-out infinite;
}

.college-page .head-stage.no-webgl #head-canvas { display: none; }
.college-page .head-stage.no-webgl .stage-fallback { display: grid; }

@keyframes collegeBob {
    0%, 100% { transform: translateY(0) rotate(-4deg); }
    50% { transform: translateY(-13px) rotate(4deg); }
}

/* قسم المحتوى الذي يحتوي على فلاتر البحث وبطاقات الامتحانات. */
.college-page .college-content {
    position: relative;
    z-index: 2;
    padding: 0 0 88px;
}

.college-page .filters-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
    margin: -42px 0 42px;
    padding: 16px;
    border: 1px solid #e8eaff;
    border-radius: 22px;
    background: #fff;
    box-shadow: 0 8px 0 #e0e7ff, 0 25px 45px rgba(15,23,42,.12);
}

.college-page .filter-select {
    flex: 1 1 200px;
    min-width: 0;
    min-height: 50px;
    padding: 12px 15px;
    border: 2px solid transparent;
    border-radius: 13px;
    outline: none;
    background: #f1f5f9;
    color: var(--college-dark);
    font: 600 1rem 'Cairo', sans-serif;
    transition: border-color .2s ease, background-color .2s ease, box-shadow .2s ease;
}

.college-page .filter-select:focus {
    border-color: #a5b4fc;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,.12);
}

.college-page .btn {
    display: inline-flex;
    min-height: 48px;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 23px;
    border: 0;
    border-radius: 13px;
    font: 800 .96rem 'Cairo', sans-serif;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
}

.college-page .btn:hover { transform: translateY(-2px); }
.college-page .btn:active { transform: translateY(1px); }

.college-page .btn-primary {
    background: linear-gradient(135deg, var(--college-primary), var(--college-purple));
    color: #fff;
    box-shadow: 0 5px 0 var(--college-deep), 0 12px 20px rgba(99,102,241,.24);
}

.college-page .btn-primary:hover { filter: brightness(1.06); }

.college-page .btn-outline {
    border: 2px solid #e2e8f0;
    background: #fff;
    color: #475569;
    box-shadow: 0 4px 0 #e2e8f0;
}

.college-page .btn-outline:hover { border-color: #a5b4fc; color: var(--college-primary-dark); }

.college-page .results-heading {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 16px;
    margin: 0 0 24px;
}

.college-page .results-heading h2 {
    margin: 0;
    color: var(--college-dark);
    font-size: clamp(1.45rem, 3vw, 2rem);
    font-weight: 900;
}

.college-page .results-heading p {
    margin: 5px 0 0;
    color: var(--college-muted);
    font-size: .95rem;
}

.college-page .results-count {
    flex: 0 0 auto;
    padding: 8px 13px;
    border-radius: 999px;
    background: #eef2ff;
    color: var(--college-primary-dark);
    font-size: .9rem;
    font-weight: 800;
}

.college-page .exams-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(min(100%, 285px), 1fr));
    gap: 25px;
}

/* بطاقة الامتحان وحركات الميل الخفيفة على الأجهزة التي تدعم المؤشر. */
.college-page .exam-card {
    position: relative;
    display: flex;
    min-width: 0;
    flex-direction: column;
    padding: 25px;
    border: 1px solid #e8eaff;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 6px 0 #eef2ff, 0 18px 34px rgba(15,23,42,.06);
    transition: transform .25s ease, box-shadow .25s ease;
}

.college-page .exam-card:hover {
    box-shadow: 0 6px 0 #dbeafe, 0 25px 42px rgba(15,23,42,.11);
}

.college-page .exam-card[data-tilt] {
    --rx: 0deg;
    --ry: 0deg;
    transform: perspective(900px) rotateX(var(--rx)) rotateY(var(--ry));
    transform-style: preserve-3d;
}

.college-page .exam-badge {
    align-self: flex-start;
    margin-bottom: 14px;
    padding: 5px 12px;
    border-radius: 8px;
    background: #eef2ff;
    color: var(--college-primary-dark);
    font-size: .8rem;
    font-weight: 800;
}

.college-page .exam-card h3 {
    flex: 1;
    margin: 0 0 18px;
    color: var(--college-dark);
    font-size: 1.1rem;
    font-weight: 800;
    line-height: 1.7;
    overflow-wrap: anywhere;
}

.college-page .exam-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 17px;
    margin-bottom: 18px;
    padding-top: 15px;
    border-top: 1px solid #f1f5f9;
    color: var(--college-muted);
    font-size: .86rem;
}

.college-page .btn-download {
    width: 100%;
    background: var(--college-dark);
    color: #fff;
    box-shadow: 0 4px 0 #020617;
}

.college-page .btn-download:hover {
    background: var(--college-primary-dark);
    box-shadow: 0 4px 0 var(--college-deep);
}

.college-page .empty-state {
    grid-column: 1 / -1;
    padding: 54px 20px;
    border: 2px dashed #cbd5e1;
    border-radius: 22px;
    background: #fff;
    color: var(--college-muted);
    text-align: center;
}

.college-page .empty-state .empty-icon { margin-bottom: 9px; font-size: 2.3rem; }
.college-page .empty-state h3 { margin: 0 0 8px; color: var(--college-dark); font-size: 1.2rem; }
.college-page .empty-state p { margin: 0; line-height: 1.8; }

/* بطاقات الإحصائيات أسفل النتائج. */
.college-page .stats-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
    margin-top: 55px;
}

.college-page .stat-card {
    padding: 22px 16px;
    border: 1px solid #e8eaff;
    border-radius: 18px;
    background: linear-gradient(145deg, #fff, #f8faff);
    text-align: center;
    box-shadow: 0 5px 0 #eef2ff;
}

.college-page .stat-number {
    display: block;
    color: var(--college-primary-dark);
    font-size: clamp(1.55rem, 4vw, 2.1rem);
    font-weight: 900;
    line-height: 1.3;
}

.college-page .stat-label { display: block; color: var(--college-muted); font-size: .9rem; }

/* إظهار العناصر بالتدرج، مع تهيئة بديلة في المتصفحات القديمة. */
.college-page .reveal {
    opacity: 0;
    translate: 0 22px;
    transition: opacity .6s ease, translate .6s ease;
}

.college-page .reveal.active { opacity: 1; translate: 0 0; }
.college-page .sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.college-page :focus-visible { outline: 3px solid var(--college-pink); outline-offset: 3px; }

@media (max-width: 900px) {
    .college-page .college-hero { padding: 55px 0 78px; }
    .college-page .college-hero-grid { grid-template-columns: 1fr; }
    .college-page .head-stage { order: -1; height: 245px; }
    .college-page .hero-copy { text-align: center; }
    .college-page .college-hero p { margin-inline: auto; }
}

@media (max-width: 600px) {
    .college-page .container { padding-inline: 15px; }
    .college-page .college-hero { padding-top: 38px; }
    .college-page .head-stage { height: 210px; }
    .college-page .filters-bar { margin-top: -35px; padding: 13px; border-radius: 18px; }
    .college-page .filter-select { flex-basis: 100%; }
    .college-page .filters-bar .btn { flex: 1 1 40%; }
    .college-page .results-heading { align-items: flex-start; flex-direction: column; }
    .college-page .exams-grid { grid-template-columns: 1fr; gap: 18px; }
    .college-page .exam-card { padding: 21px; }
    .college-page .stats-grid { gap: 10px; margin-top: 38px; }
    .college-page .stat-card { padding: 17px 7px; }
}

@media (hover: none), (prefers-reduced-motion: reduce) {
    .college-page .exam-card[data-tilt] { transform: none; }
}

@media (prefers-reduced-motion: reduce) {
    .college-page *,
    .college-page *::before,
    .college-page *::after { animation: none !important; transition: none !important; }
    .college-page .reveal { opacity: 1; translate: 0 0; }
}
</style>

<div class="college-page">
    <!-- القسم الرئيسي: عنوان الصفحة ووصفها والمجسم ثلاثي الأبعاد. -->
    <header class="college-hero">
        <div class="container college-hero-grid">
            <div class="hero-copy">
                <div class="hero-badge"><span aria-hidden="true">📚</span> موارد تعليمية للسلك الإعدادي</div>
                <h1>امتحانات ودروس السلك الإعدادي</h1>
                <p>اكتشف موارد تعليمية تساعدك على المراجعة والاستعداد للامتحانات، واختر المستوى والمادة للوصول إلى المحتوى المناسب.</p>
                <nav class="breadcrumb" aria-label="مسار التنقل">
                    <a href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/index.php">الرئيسية</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">السلك الإعدادي</span>
                </nav>
            </div>

            <!-- تحوي اللوحة رسماً ثلاثي الأبعاد؛ وتظهر الكتب emoji إذا تعذر تشغيل WebGL. -->
            <div class="head-stage" id="head-stage">
                <canvas id="head-canvas" role="img" aria-label="مجسم ثلاثي الأبعاد لكتب وقلم فوق خلفية مضيئة"></canvas>
                <div class="stage-fallback" aria-hidden="true">📚</div>
            </div>
        </div>
    </header>

    <!-- محتوى الصفحة: أدوات التصفية والنتائج والإحصائيات. -->
    <main class="college-content">
        <div class="container">
            <!-- إرسال الفلاتر عبر GET يسمح بمشاركة رابط نتائج البحث. -->
            <form class="filters-bar" method="GET" action="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/pages/college.php">
                <label class="sr-only" for="level-filter">المستوى الدراسي</label>
                <select name="level" id="level-filter" class="filter-select">
                    <option value="">جميع المستويات</option>
                    <option value="الأولى" <?= $level === 'الأولى' ? 'selected' : '' ?>>الأولى إعدادي</option>
                    <option value="الثانية" <?= $level === 'الثانية' ? 'selected' : '' ?>>الثانية إعدادي</option>
                    <option value="الثالثة" <?= $level === 'الثالثة' ? 'selected' : '' ?>>الثالثة إعدادي</option>
                </select>

                <label class="sr-only" for="subject-filter">المادة الدراسية</label>
                <select name="subject" id="subject-filter" class="filter-select">
                    <option value="">جميع المواد</option>
                    <option value="رياضيات" <?= $subject === 'رياضيات' ? 'selected' : '' ?>>الرياضيات</option>
                    <option value="فيزياء" <?= $subject === 'فيزياء' ? 'selected' : '' ?>>الفيزياء والكيمياء</option>
                    <option value="علوم الحياة" <?= $subject === 'علوم الحياة' ? 'selected' : '' ?>>علوم الحياة والأرض</option>
                    <option value="فرنسية" <?= $subject === 'فرنسية' ? 'selected' : '' ?>>اللغة الفرنسية</option>
                    <option value="عربية" <?= $subject === 'عربية' ? 'selected' : '' ?>>اللغة العربية</option>
                    <option value="إنجليزية" <?= $subject === 'إنجليزية' ? 'selected' : '' ?>>اللغة الإنجليزية</option>
                    <option value="اجتماعيات" <?= $subject === 'اجتماعيات' ? 'selected' : '' ?>>الاجتماعيات</option>
                    <option value="تربية إسلامية" <?= $subject === 'تربية إسلامية' ? 'selected' : '' ?>>التربية الإسلامية</option>
                </select>

                <button type="submit" class="btn btn-primary">تصفية النتائج</button>
                <a href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/pages/college.php" class="btn btn-outline">إعادة التعيين</a>
            </form>

            <section aria-labelledby="results-title">
                <div class="results-heading reveal">
                    <div>
                        <h2 id="results-title">امتحانات وموارد الإعدادي</h2>
                        <p>تصفح المحتوى المتوفر في قاعدة بيانات المنصة.</p>
                    </div>
                    <span class="results-count">عدد النتائج: <?= count($exams) ?></span>
                </div>

                <div class="exams-grid">
                    <?php if (empty($exams)): ?>
                        <!-- رسالة بديلة عندما لا توجد نتائج مطابقة للتصفية المحددة. -->
                        <div class="empty-state reveal">
                            <div class="empty-icon" aria-hidden="true">📭</div>
                            <h3>لا توجد نتائج مطابقة</h3>
                            <p>جرّب اختيار مستوى أو مادة أخرى، أو أعد تعيين الفلاتر لعرض جميع الموارد المتاحة.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($exams as $exam): ?>
                            <?php
                            // استخراج رابط التحميل بعد التحقق من مسار الملف.
                            $downloadUrl = $makeDownloadUrl(isset($exam['file_path']) ? (string) $exam['file_path'] : null);
                            $examTitle = (string) ($exam['title'] ?? 'امتحان دون عنوان');
                            $examCategory = (string) ($exam['category_name'] ?? 'الإعدادي');
                            $examYear = isset($exam['year']) && $exam['year'] !== null ? (string) $exam['year'] : 'غير محددة';
                            $examType = (string) ($exam['type'] ?? 'exam');
                            $typeLabels = ['exam' => 'امتحان', 'correction' => 'تصحيح', 'lesson' => 'درس'];
                            $examTypeLabel = $typeLabels[$examType] ?? 'مورد تعليمي';
                            ?>
                            <article class="exam-card reveal" data-tilt="5">
                                <span class="exam-badge"><?= htmlspecialchars($examCategory, ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($examTypeLabel, ENT_QUOTES, 'UTF-8') ?></span>
                                <h3><?= htmlspecialchars($examTitle, ENT_QUOTES, 'UTF-8') ?></h3>
                                <div class="exam-meta">
                                    <span>📅 السنة: <?= htmlspecialchars($examYear, ENT_QUOTES, 'UTF-8') ?></span>
                                    <span>⬇️ التحميلات: <?= (int) ($exam['downloads'] ?? 0) ?></span>
                                </div>
                                <?php if ($downloadUrl !== null): ?>
                                    <a href="<?= htmlspecialchars($downloadUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-download" download>
                                        <span aria-hidden="true">📥</span> تحميل الملف
                                    </a>
                                <?php else: ?>
                                    <span class="btn btn-outline" aria-label="لا يتوفر ملف لهذا المورد">لا يتوفر ملف للتحميل</span>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- تعرض هذه البطاقات أعداداً محسوبة من قاعدة البيانات الفعلية. -->
            <section class="stats-grid" aria-label="إحصائيات المنصة">
                <div class="stat-card reveal">
                    <span class="stat-number" data-count="<?= $totalExams ?>">0</span>
                    <span class="stat-label">امتحان ومورد تعليمي</span>
                </div>
                <div class="stat-card reveal">
                    <span class="stat-number" data-count="<?= $totalLessons ?>">0</span>
                    <span class="stat-label">درس مسجل</span>
                </div>
                <div class="stat-card reveal">
                    <span class="stat-number" data-count="<?= $totalUsers ?>">0</span>
                    <span class="stat-label">مستخدم مسجل</span>
                </div>
            </section>
        </div>
    </main>
</div>

<!-- تحميل مكتبة Three.js لرسم المجسم الثلاثي الأبعاد في رأس الصفحة. -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
(() => {
    'use strict';

    // احترام إعداد المستخدم لتقليل الحركة، خاصة عند تفعيل إعداد تقليل المؤثرات.
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    // إظهار العناصر تدريجياً عند دخولها إلى مجال الرؤية، مع دعم المتصفحات القديمة.
    const revealElements = document.querySelectorAll('.college-page .reveal');
    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -25px 0px' });
        revealElements.forEach((element) => revealObserver.observe(element));
    } else {
        revealElements.forEach((element) => element.classList.add('active'));
    }

    // تطبيق ميل ثلاثي الأبعاد خفيف على بطاقات الامتحانات باستخدام حركة الفأرة.
    if (canHover && !reduceMotion) {
        document.querySelectorAll('.college-page [data-tilt]').forEach((card) => {
            const maxTilt = Number.parseFloat(card.dataset.tilt) || 5;
            card.addEventListener('pointermove', (event) => {
                const rect = card.getBoundingClientRect();
                const x = (event.clientX - rect.left) / rect.width;
                const y = (event.clientY - rect.top) / rect.height;
                card.style.setProperty('--ry', (((x - 0.5) * 2 * maxTilt).toFixed(2)) + 'deg');
                card.style.setProperty('--rx', (((0.5 - y) * 2 * maxTilt).toFixed(2)) + 'deg');
            });
            card.addEventListener('pointerleave', () => {
                card.style.setProperty('--rx', '0deg');
                card.style.setProperty('--ry', '0deg');
            });
        });
    }

    // تحريك الأعداد الإحصائية من الصفر إلى القيمة الحقيقية التي أرسلها PHP.
    const counters = document.querySelectorAll('.college-page .stat-number[data-count]');
    const animateCounter = (counter) => {
        const target = Number.parseInt(counter.dataset.count || '0', 10);
        if (!Number.isFinite(target) || target < 0) {
            counter.textContent = '0';
            return;
        }
        if (reduceMotion) {
            counter.textContent = target.toLocaleString('ar-MA');
            return;
        }

        const duration = 900;
        const start = performance.now();
        const draw = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            counter.textContent = Math.floor(target * eased).toLocaleString('ar-MA');
            if (progress < 1) requestAnimationFrame(draw);
        };
        requestAnimationFrame(draw);
    };

    if ('IntersectionObserver' in window) {
        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.45 });
        counters.forEach((counter) => counterObserver.observe(counter));
    } else {
        counters.forEach(animateCounter);
    }

    // إنشاء المشهد الثلاثي الأبعاد للكتب والقلم، وإظهار رمز بديل عند الفشل.
    const stage = document.getElementById('head-stage');
    const canvas = document.getElementById('head-canvas');
    if (!stage || !canvas) return;
    if (!window.THREE) {
        stage.classList.add('no-webgl');
        return;
    }

    let renderer;
    try {
        renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
    } catch (error) {
        stage.classList.add('no-webgl');
        return;
    }

    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.outputEncoding = THREE.sRGBEncoding;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.1;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(35, 1, 0.1, 100);
    camera.position.set(0, 2.2, 12);
    camera.lookAt(0, -0.35, 0);

    // مصادر الضوء تمنح المجسم ظلالاً وانعكاسات أكثر وضوحاً.
    scene.add(new THREE.HemisphereLight(0xc7d2fe, 0x1e1b4b, 0.95));
    const keyLight = new THREE.DirectionalLight(0xffffff, 1.6);
    keyLight.position.set(4, 9, 6);
    keyLight.castShadow = true;
    keyLight.shadow.mapSize.set(1024, 1024);
    scene.add(keyLight);
    const pinkLight = new THREE.PointLight(0xec4899, 1.2, 24);
    pinkLight.position.set(-6, 1, 4);
    scene.add(pinkLight);
    const blueLight = new THREE.PointLight(0x6366f1, 1.5, 24);
    blueLight.position.set(6, -1, 5);
    scene.add(blueLight);

    // الأرضية الشفافة تستقبل الظلال دون أن تظهر كلوح صلب.
    const ground = new THREE.Mesh(
        new THREE.PlaneGeometry(30, 30),
        new THREE.ShadowMaterial({ opacity: 0.3 })
    );
    ground.rotation.x = -Math.PI / 2;
    ground.position.y = -2.15;
    ground.receiveShadow = true;
    scene.add(ground);

    const rig = new THREE.Group();
    scene.add(rig);

    // إضافة الظلال إلى القطع التي تشكل المجسم.
    const addShadow = (mesh) => {
        mesh.castShadow = true;
        mesh.receiveShadow = true;
        return mesh;
    };

    // إنشاء كتاب من غلاف ملون وطبقة صفحات فاتحة.
    function createBook(width, height, depth, color) {
        const group = new THREE.Group();
        const cover = addShadow(new THREE.Mesh(
            new THREE.BoxGeometry(width, height, depth),
            new THREE.MeshStandardMaterial({ color, roughness: 0.5, metalness: 0.06 })
        ));
        const pages = addShadow(new THREE.Mesh(
            new THREE.BoxGeometry(width * 0.96, height * 0.72, depth * 0.96),
            new THREE.MeshStandardMaterial({ color: 0xf5f0e6, roughness: 0.92 })
        ));
        pages.position.z = depth * 0.035;
        group.add(cover, pages);
        return group;
    }

    // ترتيب أربعة كتب بألوان مختلفة لتكوين كومة متدرجة.
    const books = [
        createBook(4.0, 0.52, 2.8, 0x4f46e5),
        createBook(3.6, 0.48, 2.55, 0xec4899),
        createBook(3.2, 0.44, 2.3, 0x8b5cf6),
        createBook(2.75, 0.4, 2.0, 0x0ea5e9)
    ];
    const bookY = [-1.86, -1.36, -0.9, -0.48];
    const bookRotations = [0.05, 0.22, -0.14, 0.3];
    books.forEach((book, index) => {
        book.position.set(index % 2 ? 0.1 : -0.05, bookY[index], 0);
        book.rotation.y = bookRotations[index];
        rig.add(book);
    });

    // صنع قلم يتكون من جسم خشبي ورأس ومعدن وممحاة.
    const pencilTilt = new THREE.Group();
    const pencil = new THREE.Group();
    const body = addShadow(new THREE.Mesh(
        new THREE.CylinderGeometry(0.105, 0.105, 2.05, 6),
        new THREE.MeshStandardMaterial({ color: 0xf59e0b, roughness: 0.45 })
    ));
    const wood = addShadow(new THREE.Mesh(
        new THREE.ConeGeometry(0.105, 0.32, 6),
        new THREE.MeshStandardMaterial({ color: 0xf1c9a0, roughness: 0.8 })
    ));
    wood.rotation.x = Math.PI;
    wood.position.y = -1.18;
    const graphite = addShadow(new THREE.Mesh(
        new THREE.ConeGeometry(0.04, 0.13, 6),
        new THREE.MeshStandardMaterial({ color: 0x1e293b, roughness: 0.5 })
    ));
    graphite.rotation.x = Math.PI;
    graphite.position.y = -1.38;
    const ferrule = addShadow(new THREE.Mesh(
        new THREE.CylinderGeometry(0.11, 0.11, 0.2, 18),
        new THREE.MeshStandardMaterial({ color: 0xcbd5e1, metalness: 0.7, roughness: 0.3 })
    ));
    ferrule.position.y = 1.12;
    const eraser = addShadow(new THREE.Mesh(
        new THREE.CylinderGeometry(0.105, 0.105, 0.18, 18),
        new THREE.MeshStandardMaterial({ color: 0xf472b6, roughness: 0.7 })
    ));
    eraser.position.y = 1.31;
    pencil.add(body, wood, graphite, ferrule, eraser);
    pencilTilt.add(pencil);
    pencilTilt.position.set(2.55, 0.55, 0.8);
    pencilTilt.rotation.z = -0.65;
    rig.add(pencilTilt);

    // نقاط مضيئة صغيرة تضيف عمقاً إلى المشهد.
    const particleCount = 55;
    const particlePositions = new Float32Array(particleCount * 3);
    for (let i = 0; i < particleCount; i++) {
        particlePositions[i * 3] = (Math.random() - 0.5) * 10;
        particlePositions[i * 3 + 1] = (Math.random() - 0.4) * 5;
        particlePositions[i * 3 + 2] = (Math.random() - 0.5) * 7;
    }
    const particleGeometry = new THREE.BufferGeometry();
    particleGeometry.setAttribute('position', new THREE.BufferAttribute(particlePositions, 3));
    const particles = new THREE.Points(
        particleGeometry,
        new THREE.PointsMaterial({ color: 0xc7d2fe, size: 0.055, transparent: true, opacity: 0.8, depthWrite: false })
    );
    scene.add(particles);

    // ضبط أبعاد الكاميرا مع تغير حجم نافذة المجسم.
    function resizeScene() {
        const width = stage.clientWidth;
        const height = stage.clientHeight;
        if (!width || !height) return;
        renderer.setSize(width, height, false);
        camera.aspect = width / height;
        const fitDistance = 5.6 / (Math.tan(THREE.MathUtils.degToRad(camera.fov / 2)) * camera.aspect);
        camera.position.z = Math.max(12, fitDistance);
        camera.updateProjectionMatrix();
    }

    resizeScene();
    if ('ResizeObserver' in window) {
        new ResizeObserver(resizeScene).observe(stage);
    } else {
        window.addEventListener('resize', resizeScene);
    }

    // تسمح حركة المؤشر بتدوير المجسم، ويؤدي النقر على اللوحة إلى دوران إضافي.
    const state = { rotationY: -0.45, velocityY: 0, targetX: 0, targetY: 0, wobble: 0 };
    canvas.addEventListener('pointermove', (event) => {
        if (reduceMotion) return;
        const rect = canvas.getBoundingClientRect();
        state.targetX = ((event.clientX - rect.left) / rect.width - 0.5) * 2;
        state.targetY = ((event.clientY - rect.top) / rect.height - 0.5) * 2;
    });
    canvas.addEventListener('pointerleave', () => {
        state.targetX = 0;
        state.targetY = 0;
    });
    canvas.addEventListener('click', () => {
        if (!reduceMotion) {
            state.velocityY += 0.14;
            state.wobble = 1;
        }
    });

    // إيقاف الرسم مؤقتاً عندما يكون المجسم خارج الشاشة لتقليل استهلاك الموارد.
    let sceneVisible = true;
    if ('IntersectionObserver' in window) {
        new IntersectionObserver((entries) => {
            sceneVisible = entries[0].isIntersecting;
        }).observe(stage);
    }
    const clock = new THREE.Clock();

    function renderScene() {
        requestAnimationFrame(renderScene);
        if (!sceneVisible || document.hidden) return;

        const elapsed = clock.getElapsedTime();
        if (!reduceMotion) state.rotationY += 0.0025;
        state.rotationY += state.velocityY;
        state.velocityY *= 0.94;

        rig.rotation.y += (state.rotationY + state.targetX * 0.18 - rig.rotation.y) * 0.08;
        rig.rotation.x += ((0.08 + state.targetY * 0.1) - rig.rotation.x) * 0.08;

        // اهتزاز بسيط للكتب بعد النقر، مع حركة ارتفاع خفيفة للقلم.
        state.wobble *= 0.95;
        books.forEach((book, index) => {
            book.position.x = (index % 2 ? 0.1 : -0.05) + Math.sin(elapsed * 14 + index) * state.wobble * 0.06;
        });
        if (!reduceMotion) {
            pencilTilt.position.y = 0.55 + Math.sin(elapsed * 1.2) * 0.12;
            pencil.rotation.y = elapsed * 0.5;
            particles.rotation.y = elapsed * 0.035;
        }
        renderer.render(scene, camera);
    }

    renderScene();
})();
</script>

<?php
// تضمين تذييل الموقع المشترك حتى تبقى جميع الصفحات بنفس الهيكل العام.
require_once __DIR__ . '/../includes/footer.php';
?>
