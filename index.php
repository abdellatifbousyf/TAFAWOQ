<?php require_once 'includes/header.php'; ?>

<!-- القسم الرئيسي -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="container hero-content">
        <h1 class="hero-title">
            مرحباً بك في <span class="highlight">تفوّق</span>
        </h1>
        <p class="hero-subtitle">
            المنصة التعليمية المغربية الشاملة - امتحانات، دروس، تكوينات و فرص الشغل
        </p>
        <div class="hero-search">
            <form action="/Tafawoq/pages/recherche.php" method="GET">
                <input type="text" name="q" placeholder="ابحث عن امتحان، درس، أو تكوين..." class="search-input">
                <button type="submit" class="btn btn-primary">🔍 بحث</button>
            </form>
        </div>
        <div class="hero-stats">
            <div class="stat">
                <span class="stat-number" data-count="5000">0</span>+
                <span class="stat-label">امتحان</span>
            </div>
            <div class="stat">
                <span class="stat-number" data-count="1200">0</span>+
                <span class="stat-label">درس</span>
            </div>
            <div class="stat">
                <span class="stat-number" data-count="50000">0</span>+
                <span class="stat-label">طالب</span>
            </div>
        </div>
    </div>
</section>

<!-- الأقسام -->
<section class="section categories-section">
    <div class="container">
        <h2 class="section-title">استكشف الأقسام</h2>
        <p class="section-subtitle">اختر القسم المناسب لمستواك الدراسي</p>

        <div class="categories-grid">
            <a href="/Tafawoq/pages/bac.php" class="category-card">
                <div class="category-icon">🎓</div>
                <h3>البكالوريا</h3>
                <p>امتحانات وطنية وجهوية مع التصحيح لجميع الشعب</p>
                <span class="card-arrow">←</span>
            </a>

            <a href="/Tafawoq/pages/college.php" class="category-card">
                <div class="category-icon">📚</div>
                <h3>الإعدادي</h3>
                <p>دروس و امتحانات من الأولى إلى الثالثة إعدادي</p>
                <span class="card-arrow">←</span>
            </a>

            <a href="/Tafawoq/pages/formation.php" class="category-card">
                <div class="category-icon">🔧</div>
                <h3>التكوين المهني</h3>
                <p>تكوينات OFPPT و شهادات مهنية معترف بها</p>
                <span class="card-arrow">←</span>
            </a>

            <a href="/Tafawoq/pages/emploi.php" class="category-card">
                <div class="category-icon">💼</div>
                <h3>التوظيف</h3>
                <p>مباريات التوظيف و عروض الشغل في القطاع العام و الخاص</p>
                <span class="card-arrow">←</span>
            </a>
        </div>
    </div>
</section>

<!-- آخر الامتحانات -->
<section class="section recent-section">
    <div class="container">
        <h2 class="section-title">آخر الامتحانات المضافة</h2>

        <div class="exams-grid">
            <?php
            $stmt = $pdo->query("SELECT e.*, c.name as category_name FROM exams e LEFT JOIN categories c ON e.category_id = c.id ORDER BY e.created_at DESC LIMIT 6");
            $exams = $stmt->fetchAll();

            if (empty($exams)): ?>
                <div class="empty-state" style="grid-column: 1/-1;">
                    <p>📭 لا توجد امتحانات بعد. سيتم إضافتها قريباً!</p>
                </div>
            <?php else:
                foreach ($exams as $exam): ?>
                    <div class="exam-card">
                        <div class="exam-badge"><?= htmlspecialchars($exam['category_name'] ?? 'عام') ?></div>
                        <h4><?= htmlspecialchars($exam['title']) ?></h4>
                        <div class="exam-meta">
                            <span>📅 <?= $exam['year'] ?></span>
                            <span>⬇️ <?= $exam['downloads'] ?> تحميل</span>
                        </div>
                        <?php if ($exam['file_path']): ?>
                            <a href="/Tafawoq/uploads/<?= $exam['file_path'] ?>" class="btn btn-sm btn-download" download>
                                📥 تحميل
                            </a>
                        <?php endif; ?>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>
    </div>
</section>

<!-- لماذا تفوق -->
<section class="section why-section">
    <div class="container">
        <h2 class="section-title">لماذا تفوّق؟</h2>
        <div class="features-grid">
            <div class="feature">
                <span class="feature-icon">🆓</span>
                <h4>مجاني بالكامل</h4>
                <p>جميع الموارد متاحة مجاناً لجميع التلاميذ و الطلاب</p>
            </div>
            <div class="feature">
                <span class="feature-icon">✅</span>
                <h4>محتوى موثوق</h4>
                <p>امتحانات رسمية مع التصحيح من مصادر معتمدة</p>
            </div>
            <div class="feature">
                <span class="feature-icon">🔄</span>
                <h4>تحديث مستمر</h4>
                <p>نضيف محتوى جديد يومياً لمواكبة البرنامج الدراسي</p>
            </div>
            <div class="feature">
                <span class="feature-icon">📱</span>
                <h4>متوافق مع الهاتف</h4>
                <p>تصفح و حمّل من أي جهاز بسهولة</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>