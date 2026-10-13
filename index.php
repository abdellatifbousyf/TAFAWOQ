<?php 
require_once 'includes/header.php';

// جلب آخر الامتحانات
$stmt = $pdo->query("SELECT e.*, c.name as category_name 
                     FROM exams e 
                     LEFT JOIN categories c ON e.category_id = c.id 
                     ORDER BY e.created_at DESC 
                     LIMIT 6");
$exams = $stmt->fetchAll();
?>

<style>
/* =========================================
   1. GLOBAL & TYPOGRAPHY (خط احترافي)
   ========================================= */
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap');

:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --secondary: #ec4899;
    --accent: #8b5cf6;
    --dark: #0f172a;
    --light: #f8fafc;
    --glass: rgba(255, 255, 255, 0.08);
    --glass-border: rgba(255, 255, 255, 0.15);
    --shadow-3d: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Cairo', sans-serif;
    background-color: var(--light);
    color: var(--dark);
    overflow-x: hidden;
    line-height: 1.6;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* =========================================
   2. HERO SECTION (خلفية متحركة + زجاج)
   ========================================= */
.hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
    overflow: hidden;
    padding: 100px 0;
}

/* Animated Background Blobs */
.hero::before, .hero::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.6;
    animation: floatBlob 10s infinite alternate ease-in-out;
}

.hero::before {
    width: 500px;
    height: 500px;
    background: var(--primary);
    top: -100px;
    left: -100px;
}

.hero::after {
    width: 400px;
    height: 400px;
    background: var(--secondary);
    bottom: -50px;
    right: -50px;
    animation-delay: -5s;
}

@keyframes floatBlob {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(30px, 50px) scale(1.1); }
}

.hero-content {
    position: relative;
    z-index: 10;
    text-align: center;
    max-width: 900px;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    background: var(--glass);
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 50px;
    color: #fff;
    font-weight: 700;
    font-size: 0.95rem;
    margin-bottom: 30px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    animation: fadeInDown 0.8s ease-out;
}

.hero-title {
    font-size: 4rem;
    font-weight: 900;
    color: #fff;
    line-height: 1.2;
    margin-bottom: 20px;
    animation: fadeInUp 0.8s ease-out 0.2s both;
}

.hero-title .highlight {
    background: linear-gradient(135deg, #818cf8 0%, #c084fc 50%, #f472b6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
    display: inline-block;
}

.hero-subtitle {
    font-size: 1.25rem;
    color: #cbd5e1;
    margin-bottom: 40px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    animation: fadeInUp 0.8s ease-out 0.4s both;
}

/* 3D Search Box */
.hero-search {
    max-width: 700px;
    margin: 0 auto 50px;
    animation: fadeInUp 0.8s ease-out 0.6s both;
}

.search-form {
    display: flex;
    gap: 12px;
    background: rgba(255, 255, 255, 0.95);
    padding: 8px;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.search-form:focus-within {
    transform: translateY(-5px);
    box-shadow: 0 30px 70px rgba(99, 102, 241, 0.3), 0 0 0 2px var(--primary);
}

.search-input {
    flex: 1;
    padding: 16px 24px;
    border: none;
    border-radius: 14px;
    font-size: 1.05rem;
    font-family: 'Cairo', sans-serif;
    background: transparent;
    color: var(--dark);
    outline: none;
}

.search-input::placeholder {
    color: #94a3b8;
}

.btn-search {
    padding: 16px 32px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    color: white;
    border: none;
    border-radius: 14px;
    font-weight: 700;
    font-size: 1rem;
    font-family: 'Cairo', sans-serif;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
}

.btn-search:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
}

/* Stats */
.hero-stats {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
    animation: fadeInUp 0.8s ease-out 0.8s both;
}

.stat-item {
    background: var(--glass);
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    padding: 20px 30px;
    border-radius: 20px;
    text-align: center;
    min-width: 140px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.15);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 900;
    color: #fff;
    display: block;
    line-height: 1;
}

.stat-label {
    color: #cbd5e1;
    font-size: 0.9rem;
    margin-top: 8px;
    display: block;
}

/* =========================================
   3. SECTIONS GENERAL
   ========================================= */
.section {
    padding: 100px 0;
    position: relative;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 900;
    text-align: center;
    margin-bottom: 15px;
    background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.section-subtitle {
    text-align: center;
    color: #64748b;
    font-size: 1.1rem;
    margin-bottom: 60px;
}

/* =========================================
   4. CATEGORIES (3D Cards)
   ========================================= */
.categories-section {
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 30px;
    perspective: 1000px;
}

.category-card {
    background: #fff;
    padding: 40px 30px;
    border-radius: 24px;
    text-align: center;
    text-decoration: none;
    color: var(--dark);
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.category-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    transform: scaleX(0);
    transition: transform 0.4s ease;
}

.category-card:hover {
    transform: translateY(-15px) rotateX(5deg);
    box-shadow: 0 30px 60px rgba(99, 102, 241, 0.15);
    border-color: transparent;
}

.category-card:hover::before {
    transform: scaleX(1);
}

.category-icon {
    font-size: 3.5rem;
    margin-bottom: 20px;
    display: block;
    transition: transform 0.4s ease;
    filter: drop-shadow(0 10px 10px rgba(0,0,0,0.1));
}

.category-card:hover .category-icon {
    transform: scale(1.2) translateY(-10px);
}

.category-card h3 {
    font-size: 1.4rem;
    font-weight: 800;
    margin-bottom: 12px;
}

.category-card p {
    color: #64748b;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 20px;
}

.card-arrow {
    margin-top: auto;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.category-card:hover .card-arrow {
    background: var(--primary);
    color: white;
    transform: translateX(-5px);
}

/* =========================================
   5. EXAM CARDS (Modern & Clean)
   ========================================= */
.recent-section {
    background: #fff;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 50px;
}

.btn-outline {
    padding: 10px 24px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: rgba(99, 102, 241, 0.05);
}

.exams-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
}

.exam-card {
    background: #fff;
    border-radius: 20px;
    padding: 30px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    flex-direction: column;
}

.exam-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    border-color: #e0e7ff;
}

.exam-badge {
    display: inline-block;
    padding: 6px 16px;
    background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
    color: var(--primary-dark);
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 700;
    margin-bottom: 15px;
    width: fit-content;
}

.exam-card h4 {
    font-size: 1.15rem;
    font-weight: 700;
    margin-bottom: 20px;
    line-height: 1.5;
    color: var(--dark);
    flex-grow: 1;
}

.exam-meta {
    display: flex;
    gap: 20px;
    color: #64748b;
    font-size: 0.85rem;
    margin-bottom: 20px;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}

.exam-meta span {
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-download {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 14px;
    background: var(--dark);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.btn-download:hover {
    background: var(--primary);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
    background: #f8fafc;
    border-radius: 24px;
    border: 2px dashed #e2e8f0;
    color: #64748b;
    font-size: 1.1rem;
}

/* =========================================
   6. FEATURES & CTA
   ========================================= */
.why-section {
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}

.feature {
    text-align: center;
    padding: 40px 30px;
    background: #fff;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    transition: all 0.3s ease;
}

.feature:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
    border-color: #e0e7ff;
}

.feature-icon {
    font-size: 3rem;
    display: block;
    margin-bottom: 20px;
}

.feature h4 {
    font-size: 1.2rem;
    font-weight: 800;
    margin-bottom: 10px;
    color: var(--dark);
}

.feature p {
    color: #64748b;
    font-size: 0.95rem;
}

.cta-section {
    padding: 100px 0;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    position: relative;
    overflow: hidden;
}

.cta-section::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
    animation: rotate 30s linear infinite;
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.cta-box {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 60px 40px;
    border-radius: 32px;
    text-align: center;
    position: relative;
    z-index: 10;
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
}

.cta-box h2 {
    color: #fff;
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: 20px;
}

.cta-box p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.15rem;
    margin-bottom: 40px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-lg {
    padding: 16px 40px;
    font-size: 1.1rem;
    border-radius: 16px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.btn-white {
    background: #fff;
    color: var(--primary);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.btn-white:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.btn-glass {
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.btn-glass:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-3px);
}

/* =========================================
   7. ANIMATIONS & RESPONSIVE
   ========================================= */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.reveal {
    opacity: 0;
    transform: translateY(40px);
    transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
}

.reveal.active {
    opacity: 1;
    transform: translateY(0);
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .search-form {
        flex-direction: column;
        background: transparent;
        box-shadow: none;
        gap: 15px;
    }
    
    .search-input {
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .btn-search {
        width: 100%;
    }
    
    .hero-stats {
        gap: 15px;
    }
    
    .stat-item {
        min-width: 100px;
        padding: 15px 20px;
    }
    
    .section-header {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .cta-box h2 {
        font-size: 1.8rem;
    }
}
</style>

<!-- Hero Section -->
<section class="hero">
    <div class="container hero-content">
        <div class="hero-badge">
            <span>🇲🇦</span> المنصة التعليمية الأولى في المغرب
        </div>
        <h1 class="hero-title">
            مرحباً بك في <span class="highlight">تفوّق</span>
        </h1>
        <p class="hero-subtitle">
            وجهتك الشاملة للامتحانات الوطنية، الدروس الملخصة، التكوين المهني، وفرص التوظيف في مكان واحد.
        </p>
        
        <div class="hero-search">
            <form action="recherche.php" method="GET" class="search-form">
                <input type="text" name="q" placeholder="ابحث عن امتحان، درس، أو تكوين..." class="search-input" required>
                <button type="submit" class="btn-search">🔍 بحث</button>
            </form>
        </div>

        <div class="hero-stats">
            <div class="stat-item">
                <span class="stat-number" data-count="5000">0</span>
                <span class="stat-label">امتحان</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-count="1200">0</span>
                <span class="stat-label">درس</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-count="50000">0</span>
                <span class="stat-label">طالب</span>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section categories-section">
    <div class="container">
        <h2 class="section-title reveal">استكشف الأقسام</h2>
        <p class="section-subtitle reveal">اختر المسار المناسب لمستواك الدراسي والمهني</p>

        <div class="categories-grid">
            <a href="bac.php" class="category-card reveal">
                <div class="category-icon">🎓</div>
                <h3>البكالوريا</h3>
                <p>امتحانات وطنية وجهوية مع التصحيح النموذجي لجميع الشعب</p>
                <div class="card-arrow">←</div>
            </a>

            <a href="college.php" class="category-card reveal">
                <div class="category-icon">📚</div>
                <h3>السلك الإعدادي</h3>
                <p>دروس ملخصة وامتحانات محلية وجهوية من الأولى إلى الثالثة</p>
                <div class="card-arrow">←</div>
            </a>

            <a href="formation.php" class="category-card reveal">
                <div class="category-icon">🔧</div>
                <h3>التكوين المهني</h3>
                <p>امتحانات وتكوينات OFPPT وشهادات مهنية معترف بها</p>
                <div class="card-arrow">←</div>
            </a>

            <a href="emploi.php" class="category-card reveal">
                <div class="category-icon">💼</div>
                <h3>التوظيف والمباريات</h3>
                <p>أحدث عروض الشغل ومباريات القطاع العام والخاص</p>
                <div class="card-arrow">←</div>
            </a>
        </div>
    </div>
</section>

<!-- Recent Exams Section -->
<section class="section recent-section">
    <div class="container">
        <div class="section-header reveal">
            <h2 class="section-title" style="text-align: right; margin: 0;">آخر الامتحانات المضافة</h2>
            <a href="recherche.php" class="btn-outline">عرض جميع الامتحانات ←</a>
        </div>

        <div class="exams-grid">
            <?php if (empty($exams)): ?>
                <div class="empty-state reveal">
                    <p>📭 لا توجد امتحانات مضافة بعد. سيتم تحديث المحتوى قريباً!</p>
                </div>
            <?php else: ?>
                <?php foreach ($exams as $exam): ?>
                    <div class="exam-card reveal">
                        <div class="exam-badge"><?= htmlspecialchars($exam['category_name'] ?? 'عام') ?></div>
                        <h4><?= htmlspecialchars($exam['title']) ?></h4>
                        <div class="exam-meta">
                            <span>📅 <?= htmlspecialchars($exam['year']) ?></span>
                            <span>⬇️ <?= (int)$exam['downloads'] ?> تحميل</span>
                        </div>
                        <?php if (!empty($exam['file_path'])): ?>
                            <a href="uploads/<?= htmlspecialchars($exam['file_path']) ?>" class="btn-download" download>
                                <span>📥</span> تحميل الملف
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Why Section -->
<section class="section why-section">
    <div class="container">
        <h2 class="section-title reveal">لماذا تختار منصة تفوّق؟</h2>
        <div class="features-grid">
            <div class="feature reveal">
                <span class="feature-icon">🆓</span>
                <h4>مجاني بالكامل</h4>
                <p>جميع الموارد التعليمية متاحة مجاناً وبدون أي قيود لجميع التلاميذ</p>
            </div>
            <div class="feature reveal">
                <span class="feature-icon">✅</span>
                <h4>محتوى موثوق ومعتمد</h4>
                <p>امتحانات رسمية مع التصحيح النموذجي من مصادر وزارة التربية الوطنية</p>
            </div>
            <div class="feature reveal">
                <span class="feature-icon">🔄</span>
                <h4>تحديث يومي مستمر</h4>
                <p>نضيف محتوى جديد ودروساً ملخصة بشكل أسبوعي لمواكبة البرنامج</p>
            </div>
            <div class="feature reveal">
                <span class="feature-icon">📱</span>
                <h4>تجربة سلسة على الهاتف</h4>
                <p>تصفح، ابحث، وحمّل امتحاناتك بسهولة من أي جهاز ذكي أو حاسوب</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container">
        <div class="cta-box reveal">
            <h2>🚀 ابدأ رحلة التفوق والنجاح الآن</h2>
            <p>سجّل حسابك مجاناً واحصل على إمكانية تحميل الامتحانات، حفظ المفضلة، ومتابعة آخر المستجدات.</p>
            <?php if (!isset($_SESSION['user']) && !isset($_SESSION['admin'])): ?>
                <div class="cta-buttons">
                    <a href="register.php" class="btn-lg btn-white">إنشاء حساب مجاني</a>
                    <a href="login.php" class="btn-lg btn-glass">تسجيل الدخول</a>
                </div>
            <?php else: ?>
                <a href="user/dashboard.php" class="btn-lg btn-white">الذهاب إلى لوحة التحكم</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
// 1. Animated Counter
const counters = document.querySelectorAll('.stat-number');
const speed = 150;

const animateCounters = () => {
    counters.forEach(counter => {
        const target = +counter.getAttribute('data-count');
        const count = +counter.innerText;
        const inc = target / speed;

        if (count < target) {
            counter.innerText = Math.ceil(count + inc);
            setTimeout(animateCounters, 20);
        } else {
            counter.innerText = target.toLocaleString(); // Add commas for thousands
        }
    });
};

// Trigger counter when in view
const observerCounter = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounters();
            observerCounter.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

if (counters.length > 0) {
    observerCounter.observe(counters[0].parentElement.parentElement);
}

// 2. Scroll Reveal Animation
const revealElements = document.querySelectorAll('.reveal');

const revealOnScroll = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
            // Add staggered delay for grid items
            setTimeout(() => {
                entry.target.classList.add('active');
            }, index * 100);
            revealOnScroll.unobserve(entry.target);
        }
    });
}, { 
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px"
});

revealElements.forEach(el => revealOnScroll.observe(el));
</script>

<?php require_once 'includes/footer.php'; ?>