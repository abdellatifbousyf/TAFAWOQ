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
/* 3D Hero Section */
.hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    perspective: 1000px;
}

.hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255,255,255,0.1) 0%, transparent 50%);
    transform: translateZ(-100px);
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    transform-style: preserve-3d;
    animation: floatIn 1s ease-out;
}

@keyframes floatIn {
    0% {
        opacity: 0;
        transform: translateY(50px) rotateX(10deg);
    }
    100% {
        opacity: 1;
        transform: translateY(0) rotateX(0);
    }
}

.hero-badge {
    display: inline-block;
    padding: 12px 30px;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    border-radius: 50px;
    color: white;
    font-weight: bold;
    margin-bottom: 30px;
    border: 2px solid rgba(255,255,255,0.3);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    transform: translateZ(50px);
}

.hero-title {
    font-size: 3.5rem;
    color: white;
    margin-bottom: 20px;
    text-shadow: 0 10px 30px rgba(0,0,0,0.3);
    transform: translateZ(30px);
}

.hero-title .highlight {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: inline-block;
    transform: rotateY(-10deg) scale(1.1);
    transition: transform 0.3s;
}

.hero-title .highlight:hover {
    transform: rotateY(0) scale(1.2);
}

.hero-subtitle {
    font-size: 1.3rem;
    color: rgba(255,255,255,0.9);
    margin-bottom: 40px;
    transform: translateZ(20px);
}

/* 3D Search Box */
.hero-search {
    max-width: 700px;
    margin: 0 auto 50px;
    transform: translateZ(40px);
}

.hero-search form {
    display: flex;
    gap: 15px;
    background: rgba(255,255,255,0.95);
    padding: 10px;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    transform: perspective(1000px) rotateX(5deg);
    transition: transform 0.3s;
}

.hero-search form:hover {
    transform: perspective(1000px) rotateX(0deg) translateY(-5px);
}

.search-input {
    flex: 1;
    padding: 15px 25px;
    border: none;
    border-radius: 15px;
    font-size: 1rem;
    background: #f5f5f5;
    transition: all 0.3s;
}

.search-input:focus {
    outline: none;
    background: white;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

/* 3D Stats */
.hero-stats {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
}

.stat {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    padding: 30px 40px;
    border-radius: 20px;
    text-align: center;
    border: 2px solid rgba(255,255,255,0.3);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    transform: translateZ(60px);
    transition: all 0.3s;
}

.stat:hover {
    transform: translateZ(80px) translateY(-10px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
}

.stat-number {
    font-size: 3rem;
    font-weight: bold;
    color: white;
    display: block;
}

.stat-label {
    color: rgba(255,255,255,0.9);
    font-size: 1rem;
    margin-top: 10px;
    display: block;
}

/* 3D Categories */
.categories-section {
    padding: 100px 0;
    background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
}

.section-title {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.section-subtitle {
    text-align: center;
    color: #666;
    margin-bottom: 60px;
    font-size: 1.1rem;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    perspective: 1000px;
}

.category-card {
    background: white;
    padding: 40px 30px;
    border-radius: 25px;
    text-align: center;
    text-decoration: none;
    color: #333;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    transform-style: preserve-3d;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.category-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    opacity: 0;
    transition: opacity 0.3s;
    z-index: 0;
}

.category-card:hover::before {
    opacity: 1;
}

.category-card:hover {
    transform: translateY(-15px) rotateX(10deg) scale(1.05);
    box-shadow: 0 30px 60px rgba(102, 126, 234, 0.4);
    border-color: #667eea;
}

.category-card > * {
    position: relative;
    z-index: 1;
}

.category-card:hover h3,
.category-card:hover p {
    color: white;
}

.category-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    display: block;
    transition: transform 0.3s;
}

.category-card:hover .category-icon {
    transform: translateZ(30px) scale(1.2);
}

.category-card h3 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    transition: color 0.3s;
}

.category-card p {
    color: #666;
    line-height: 1.6;
    transition: color 0.3s;
}

.card-arrow {
    position: absolute;
    bottom: 20px;
    right: 30px;
    font-size: 2rem;
    color: #667eea;
    opacity: 0;
    transform: translateX(-20px);
    transition: all 0.3s;
}

.category-card:hover .card-arrow {
    opacity: 1;
    transform: translateX(0);
    color: white;
}

/* 3D Exam Cards */
.recent-section {
    padding: 100px 0;
    background: white;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 50px;
}

.exams-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    perspective: 1000px;
}

.exam-card {
    background: white;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    transform-style: preserve-3d;
    border: 2px solid #f0f0f0;
    position: relative;
    overflow: hidden;
}

.exam-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transform: scaleX(0);
    transition: transform 0.3s;
}

.exam-card:hover {
    transform: translateY(-10px) rotateX(5deg);
    box-shadow: 0 25px 50px rgba(102, 126, 234, 0.25);
    border-color: #667eea;
}

.exam-card:hover::after {
    transform: scaleX(1);
}

.exam-badge {
    display: inline-block;
    padding: 8px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: bold;
    margin-bottom: 15px;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.exam-card h4 {
    font-size: 1.2rem;
    margin-bottom: 15px;
    color: #333;
    line-height: 1.4;
}

.exam-meta {
    display: flex;
    gap: 20px;
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 20px;
}

.btn-download {
    display: inline-block;
    padding: 12px 25px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 10px;
    font-weight: bold;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-download:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
}

/* Features 3D */
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 40px;
    margin-top: 60px;
}

.feature {
    text-align: center;
    padding: 40px 30px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    transition: all 0.4s;
    transform-style: preserve-3d;
}

.feature:hover {
    transform: translateY(-15px) rotateX(10deg);
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
}

.feature-icon {
    font-size: 3.5rem;
    display: block;
    margin-bottom: 20px;
    filter: drop-shadow(0 5px 10px rgba(0,0,0,0.1));
}

/* CTA Section 3D */
.cta-section {
    padding: 100px 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.cta-box {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(20px);
    padding: 60px 40px;
    border-radius: 30px;
    text-align: center;
    border: 2px solid rgba(255,255,255,0.2);
    box-shadow: 0 25px 50px rgba(0,0,0,0.2);
    position: relative;
    z-index: 1;
    transform: perspective(1000px) rotateX(5deg);
    transition: transform 0.3s;
}

.cta-box:hover {
    transform: perspective(1000px) rotateX(0deg) translateY(-10px);
}

.cta-box h2 {
    color: white;
    font-size: 2.5rem;
    margin-bottom: 20px;
}

.cta-box p {
    color: rgba(255,255,255,0.9);
    font-size: 1.2rem;
    margin-bottom: 40px;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-lg {
    padding: 18px 40px;
    font-size: 1.1rem;
    border-radius: 15px;
}

.btn-primary {
    background: white;
    color: #667eea;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.btn-primary:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
}

.btn-outline {
    border: 2px solid white;
    color: white;
    background: transparent;
}

.btn-outline:hover {
    background: white;
    color: #667eea;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px;
    background: #f8f9fa;
    border-radius: 20px;
    color: #666;
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-search form {
        flex-direction: column;
    }
    
    .hero-stats {
        gap: 20px;
    }
    
    .stat {
        padding: 20px 30px;
    }
    
    .section-header {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
}
</style>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="container hero-content">
        <div class="hero-badge">🇲🇦 المنصة الأولى في المغرب</div>
        <h1 class="hero-title">
            مرحباً بك في <span class="highlight">تفوّق</span>
        </h1>
        <p class="hero-subtitle">
            المنصة التعليمية المغربية الشاملة - امتحانات، دروس، تكوينات و فرص الشغل
        </p>
        
        <div class="hero-search">
            <form action="recherche.php" method="GET">
                <input type="text" name="q" placeholder="ابحث عن امتحان، درس، أو تكوين..." class="search-input" required>
                <button type="submit" class="btn btn-primary"> بحث</button>
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

<!-- Categories Section -->
<section class="section categories-section">
    <div class="container">
        <h2 class="section-title">استكشف الأقسام</h2>
        <p class="section-subtitle">اختر القسم المناسب لمستواك الدراسي</p>

        <div class="categories-grid">
            <a href="bac.php" class="category-card">
                <div class="category-icon">🎓</div>
                <h3>البكالوريا</h3>
                <p>امتحانات وطنية وجهوية مع التصحيح لجميع الشعب</p>
                <span class="card-arrow">←</span>
            </a>

            <a href="college.php" class="category-card">
                <div class="category-icon">📚</div>
                <h3>الإعدادي</h3>
                <p>دروس و امتحانات من الأولى إلى الثالثة إعدادي</p>
                <span class="card-arrow">←</span>
            </a>

            <a href="formation.php" class="category-card">
                <div class="category-icon">🔧</div>
                <h3>التكوين المهني</h3>
                <p>تكوينات OFPPT و شهادات مهنية معترف بها</p>
                <span class="card-arrow">←</span>
            </a>

            <a href="emploi.php" class="category-card">
                <div class="category-icon">💼</div>
                <h3>التوظيف</h3>
                <p>مباريات التوظيف و عروض الشغل في القطاع العام و الخاص</p>
                <span class="card-arrow">←</span>
            </a>
        </div>
    </div>
</section>

<!-- Recent Exams Section -->
<section class="section recent-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">آخر الامتحانات المضافة</h2>
            <a href="recherche.php" class="btn btn-outline btn-sm">عرض الكل ←</a>
        </div>

        <div class="exams-grid">
            <?php if (empty($exams)): ?>
                <div class="empty-state">
                    <p>📭 لا توجد امتحانات بعد. سيتم إضافتها قريباً!</p>
                </div>
            <?php else: ?>
                <?php foreach ($exams as $exam): ?>
                    <div class="exam-card">
                        <div class="exam-badge"><?= htmlspecialchars($exam['category_name'] ?? 'عام') ?></div>
                        <h4><?= htmlspecialchars($exam['title']) ?></h4>
                        <div class="exam-meta">
                            <span>📅 <?= htmlspecialchars($exam['year']) ?></span>
                            <span>⬇️ <?= (int)$exam['downloads'] ?> تحميل</span>
                        </div>
                        <?php if (!empty($exam['file_path'])): ?>
                            <a href="uploads/<?= htmlspecialchars($exam['file_path']) ?>" class="btn-download" download>
                                 تحميل
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Why Section -->
<section class="section why-section" style="padding: 100px 0; background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);">
    <div class="container">
        <h2 class="section-title">لماذا تفوّق؟</h2>
        <div class="features-grid">
            <div class="feature">
                <span class="feature-icon"></span>
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

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container">
        <div class="cta-box">
            <h2>🚀 ابدأ رحلة التفوق الآن</h2>
            <p>سجّل حسابك مجاناً واحصل على إمكانية تحميل الامتحانات وحفظها في حسابك</p>
            <?php if (!isset($_SESSION['user']) && !isset($_SESSION['admin'])): ?>
                <div class="cta-buttons">
                    <a href="register.php" class="btn btn-primary btn-lg">إنشاء حساب مجاني</a>
                    <a href="login.php" class="btn btn-outline btn-lg">تسجيل الدخول</a>
                </div>
            <?php else: ?>
                <a href="user/dashboard.php" class="btn btn-primary btn-lg">الذهاب إلى حسابي</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
// Animated Counter
const counters = document.querySelectorAll('.stat-number');
const speed = 200;

const animateCounters = () => {
    counters.forEach(counter => {
        const target = +counter.getAttribute('data-count');
        const count = +counter.innerText;
        const inc = target / speed;

        if (count < target) {
            counter.innerText = Math.ceil(count + inc);
            setTimeout(animateCounters, 20);
        } else {
            counter.innerText = target;
        }
    });
};

// Trigger animation when in view
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounters();
            observer.unobserve(entry.target);
        }
    });
});

if (counters.length > 0) {
    observer.observe(counters[0].parentElement.parentElement);
}

// 3D Tilt Effect for Cards
document.querySelectorAll('.category-card, .exam-card, .feature').forEach(card => {
    card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        const rotateX = (y - centerY) / 10;
        const rotateY = (centerX - x) / 10;
        
        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(10px)`;
    });
    
    card.addEventListener('mouseleave', () => {
        card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateZ(0)';
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>