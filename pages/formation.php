
<?php
require_once __DIR__ . '/../includes/header.php';

$domain = trim($_GET['domain'] ?? '');

// التحقق من التخصص المختار
$allowedDomains = ['تطوير', 'محاسبة', 'كهرباء', 'سياحة', 'صحة'];

if (!in_array($domain, $allowedDomains, true)) {
    $domain = '';
}

// جلب التكوينات المهنية
$where = "WHERE c.slug = 'formation'";
$params = [];

if ($domain !== '') {
    $where .= " AND e.title LIKE ?";
    $params[] = '%' . $domain . '%';
}

$sql = "
    SELECT e.*, c.name AS category_name
    FROM exams e
    JOIN categories c ON e.category_id = c.id
    $where
    ORDER BY e.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.formation-page {
    direction: rtl;
    padding-bottom: 60px;
    background: #f7f7fc;
    min-height: 70vh;
}

.formation-hero {
    position: relative;
    overflow: hidden;
    padding: 75px 20px 65px;
    color: white;
    text-align: center;
    background: linear-gradient(135deg, #17132f, #4c1d95, #7c3aed);
}

.formation-hero::before,
.formation-hero::after {
    content: "";
    position: absolute;
    width: 230px;
    height: 230px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
    filter: blur(2px);
}

.formation-hero::before {
    top: -100px;
    right: -50px;
}

.formation-hero::after {
    bottom: -140px;
    left: -40px;
}

.formation-hero .container {
    position: relative;
    z-index: 1;
}

.formation-hero .hero-icon {
    font-size: 65px;
    display: inline-block;
    margin-bottom: 15px;
    animation: formationFloat 3s ease-in-out infinite;
}

.formation-hero h1 {
    color: #fff;
    font-size: clamp(28px, 5vw, 44px);
    font-weight: 800;
    margin: 0 0 15px;
}

.formation-hero p {
    color: #eee7ff;
    font-size: 17px;
    max-width: 700px;
    margin: 0 auto 22px;
    line-height: 1.9;
}

.formation-breadcrumb {
    font-size: 14px;
    color: #e9ddff;
}

.formation-breadcrumb a {
    color: #fff;
    text-decoration: none;
}

.formation-content {
    width: min(1150px, 92%);
    margin: 35px auto 0;
}

.formation-features {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 20px;
    margin-bottom: 35px;
}

.formation-feature {
    background: white;
    border: 1px solid #eee9fb;
    border-radius: 18px;
    padding: 25px 18px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(36, 20, 75, .05);
    transition: transform .25s, box-shadow .25s;
}

.formation-feature:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(76, 29, 149, .12);
}

.formation-feature .feature-icon {
    font-size: 36px;
    display: block;
    margin-bottom: 12px;
}

.formation-feature h4 {
    margin: 0 0 10px;
    color: #312052;
    font-size: 19px;
}

.formation-feature p {
    margin: 0;
    color: #77728a;
    font-size: 14px;
    line-height: 1.8;
}

.formation-filters {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    background: white;
    padding: 18px;
    margin-bottom: 25px;
    border: 1px solid #eee9fb;
    border-radius: 16px;
}

.formation-filters select {
    flex: 1;
    min-width: 200px;
    padding: 13px 15px;
    border: 1px solid #ded7ee;
    border-radius: 10px;
    background: #fff;
    color: #312052;
    font-size: 15px;
    outline: none;
}

.formation-filters select:focus {
    border-color: #7c3aed;
}

.formation-filters button {
    padding: 13px 24px;
    border: none;
    border-radius: 10px;
    background: #6d28d9;
    color: white;
    font-weight: 700;
    cursor: pointer;
    transition: background .2s;
}

.formation-filters button:hover {
    background: #4c1d95;
}

.formation-results-title {
    color: #2e2147;
    font-size: 23px;
    font-weight: 800;
    margin: 30px 0 20px;
}

.formation-exams-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 22px;
}

.formation-exam-card {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    background: white;
    padding: 24px;
    border-radius: 18px;
    border: 1px solid #eee9fb;
    box-shadow: 0 8px 25px rgba(36, 20, 75, .05);
    transition: transform .25s, box-shadow .25s;
}

.formation-exam-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 35px rgba(76, 29, 149, .12);
}

.formation-exam-icon {
    font-size: 32px;
    margin-bottom: 12px;
}

.formation-exam-badge {
    display: inline-block;
    background: #f0e8ff;
    color: #6d28d9;
    font-size: 12px;
    font-weight: 700;
    border-radius: 20px;
    padding: 7px 12px;
    margin-bottom: 15px;
}

.formation-exam-card h4 {
    color: #2e2147;
    font-size: 18px;
    line-height: 1.8;
    margin: 0 0 15px;
    overflow-wrap: anywhere;
}

.formation-exam-meta {
    color: #77728a;
    font-size: 14px;
    margin-bottom: 20px;
}

.formation-download {
    display: inline-block;
    margin-top: auto;
    padding: 11px 18px;
    background: #6d28d9;
    color: white;
    text-decoration: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    transition: background .2s;
}

.formation-download:hover {
    background: #4c1d95;
    color: white;
}

.formation-empty {
    grid-column: 1 / -1;
    background: white;
    border: 1px dashed #cfc2e8;
    border-radius: 18px;
    padding: 45px 20px;
    text-align: center;
    color: #77728a;
    line-height: 2;
}

@keyframes formationFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

@media (max-width: 900px) {
    .formation-features,
    .formation-exams-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 600px) {
    .formation-hero {
        padding: 55px 15px 45px;
    }

    .formation-hero p {
        font-size: 15px;
    }

    .formation-features,
    .formation-exams-grid {
        grid-template-columns: 1fr;
    }

    .formation-filters {
        align-items: stretch;
    }

    .formation-filters select,
    .formation-filters button {
        width: 100%;
    }

    .formation-content {
        width: 94%;
    }
}
</style>

<div class="formation-page">

    <section class="formation-hero">
        <div class="container">
            <span class="hero-icon">🎓</span>

            <h1>التكوين المهني</h1>

            <p>
                اكتشف نماذج الامتحانات والتكوينات المهنية،
                واستعد للامتحانات النهائية بمساعدة منصة تفوق.
            </p>

            <div class="formation-breadcrumb">
                <a href="<?= htmlspecialchars($base ?? '/TAFAWOQ') ?>/index.php">
                    الرئيسية
                </a>
                / <span>التكوين المهني</span>
            </div>
        </div>
    </section>

    <div class="formation-content">

        <section class="formation-features">
            <div class="formation-feature">
                <span class="feature-icon">🏫</span>
                <h4>OFPPT</h4>
                <p>نماذج وموارد للاستعداد لمسارك في التكوين المهني.</p>
            </div>

            <div class="formation-feature">
                <span class="feature-icon">📜</span>
                <h4>الامتحانات النهائية</h4>
                <p>راجع الدروس وتدرّب على نماذج الامتحانات المتاحة.</p>
            </div>

            <div class="formation-feature">
                <span class="feature-icon">💻</span>
                <h4>تخصصات متنوعة</h4>
                <p>الإعلاميات، المحاسبة، الكهرباء، السياحة ومجالات أخرى.</p>
            </div>
        </section>

        <form class="formation-filters" method="GET"
              action="<?= htmlspecialchars($base ?? '/TAFAWOQ') ?>/pages/formation.php">

            <select name="domain">
                <option value="">كل التخصصات</option>

                <option value="تطوير" <?= $domain === 'تطوير' ? 'selected' : '' ?>>
                    التطوير الرقمي والإعلاميات
                </option>

                <option value="محاسبة" <?= $domain === 'محاسبة' ? 'selected' : '' ?>>
                    المحاسبة والتدبير
                </option>

                <option value="كهرباء" <?= $domain === 'كهرباء' ? 'selected' : '' ?>>
                    الكهرباء والصيانة
                </option>

                <option value="سياحة" <?= $domain === 'سياحة' ? 'selected' : '' ?>>
                    السياحة والفندقة
                </option>

                <option value="صحة" <?= $domain === 'صحة' ? 'selected' : '' ?>>
                    الصحة والتمريض
                </option>
            </select>

            <button type="submit">🔎 تصفية النتائج</button>
        </form>

        <h2 class="formation-results-title">📚 نماذج التكوين المهني</h2>

        <section class="formation-exams-grid">

            <?php if (empty($exams)): ?>

                <div class="formation-empty">
                    <div style="font-size:42px;">📭</div>
                    <h3>لا توجد نتائج حالياً</h3>
                    <p>جرّب تخصصاً آخر أو عُد لاحقاً للاطلاع على المحتوى الجديد.</p>
                </div>

            <?php else: ?>

                <?php foreach ($exams as $exam): ?>

                    <article class="formation-exam-card">
                        <span class="formation-exam-icon">📘</span>

                        <span class="formation-exam-badge">
                            تكوين مهني
                        </span>

                        <h4>
                            <?= htmlspecialchars($exam['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </h4>

                        <div class="formation-exam-meta">
                            📅 السنة:
                            <?= htmlspecialchars((string)($exam['year'] ?? 'غير محددة'), ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <?php
                        // منع استعمال مسارات غير آمنة من قاعدة البيانات
                        $filePath = str_replace('\\', '/', (string)($exam['file_path'] ?? ''));
                        $filePath = ltrim($filePath, '/');

                        $safeFile = $filePath !== ''
                            && !str_contains($filePath, '..')
                            && preg_match('#^[a-zA-Z0-9_\-/\.]+$#', $filePath);
                        ?>

                        <?php if ($safeFile): ?>
                            <a
                                href="<?= htmlspecialchars(($base ?? '/TAFAWOQ') . '/uploads/' . implode('/', array_map('rawurlencode', explode('/', $filePath))), ENT_QUOTES, 'UTF-8') ?>"
                                class="formation-download"
                                download
                            >
                                📥 تحميل الملف
                            </a>
                        <?php endif; ?>
                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </section>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
