<?php
require_once '../includes/header.php';

$query = trim($_GET['q'] ?? '');
$results = [];
$count = 0;

if (strlen($query) >= 2) {
    $searchTerm = "%$query%";
    $stmt = $pdo->prepare("
        SELECT e.*, c.name as category_name, c.slug as category_slug
        FROM exams e
        LEFT JOIN categories c ON e.category_id = c.id
        WHERE e.title LIKE ?
        ORDER BY e.created_at DESC
        LIMIT 50
    ");
    $stmt->execute([$searchTerm]);
    $results = $stmt->fetchAll();
    $count = count($results);
}
?>

<div class="page-header">
    <div class="container">
        <h1>🔍 البحث</h1>
        <p>ابحث في جميع الامتحانات و الدروس و التكوينات</p>
    </div>
</div>

<section class="section">
    <div class="container">

        <div class="search-box">
            <form action="recherche.php" method="GET">
                <input type="text" name="q" class="search-input"
                       value="<?= htmlspecialchars($query) ?>"
                       placeholder="مثال: بكالوريا 2025 رياضيات...">
                <button type="submit" class="btn btn-primary">بحث</button>
            </form>
        </div>

        <?php if ($query && strlen($query) >= 2): ?>
            <p class="search-results-info">
                تم العثور على <strong><?= $count ?></strong> نتيجة لـ "<strong><?= htmlspecialchars($query) ?></strong>"
            </p>

            <div class="exams-grid">
                <?php if (empty($results)): ?>
                    <div class="empty-state" style="grid-column:1/-1;">
                        <p>😕 لم نجد نتائج. جرّب كلمات مختلفة.</p>
                    </div>
                <?php else:
                    foreach ($results as $exam): ?>
                        <div class="exam-card">
                            <div class="exam-badge"><?= htmlspecialchars($exam['category_name'] ?? 'عام') ?></div>
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

        <?php elseif ($query): ?>
            <div class="alert alert-info" style="text-align:center;">
                ⚠️ الرجاء إدخال كلمتين على الأقل للبحث.
            </div>
        <?php endif; ?>

    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
