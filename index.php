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
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap');

:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --primary-deep: #3730a3;
    --secondary: #ec4899;
    --accent: #8b5cf6;
    --dark: #0f172a;
    --light: #f8fafc;
    --glass: rgba(255, 255, 255, 0.08);
    --glass-border: rgba(255, 255, 255, 0.15);
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Cairo', sans-serif;
    background-color: var(--light);
    color: var(--dark);
    overflow-x: hidden;
    line-height: 1.6;
}

.container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

/* =========================================
   3D TILT SYSTEM (cards)
   ========================================= */
[data-tilt] {
    --rx: 0deg; --ry: 0deg; --mx: 50%; --my: 50%;
    transform: perspective(900px) rotateX(var(--rx)) rotateY(var(--ry));
    transform-style: preserve-3d;
    transition: transform .6s cubic-bezier(.2,.8,.2,1), box-shadow .4s ease;
    will-change: transform;
}
[data-tilt].tilting { transition: transform .12s ease-out, box-shadow .4s ease; }
[data-tilt]::after {
    content: '';
    position: absolute; inset: 0;
    border-radius: inherit;
    background: radial-gradient(circle at var(--mx) var(--my), rgba(255,255,255,.55), transparent 55%);
    opacity: 0; pointer-events: none;
    transition: opacity .3s ease;
    mix-blend-mode: soft-light;
}
[data-tilt].tilting::after { opacity: 1; }

.reveal { opacity: 0; translate: 0 50px; transition: opacity .8s ease, translate .8s cubic-bezier(.2,.8,.2,1); }
.reveal.active { opacity: 1; translate: 0 0; }

/* =========================================
   HERO: text + real 3D stage
   ========================================= */
.hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #0b1024 0%, #1e1b4b 55%, #312e81 100%);
    overflow: hidden;
    padding: 110px 0 70px;
}
.hero::before, .hero::after {
    content: ''; position: absolute; border-radius: 50%;
    filter: blur(90px); opacity: .5;
    animation: floatBlob 10s infinite alternate ease-in-out;
}
.hero::before { width: 480px; height: 480px; background: var(--primary); top: -120px; left: -100px; }
.hero::after  { width: 380px; height: 380px; background: var(--secondary); bottom: -80px; right: -60px; animation-delay: -5s; }
@keyframes floatBlob { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(30px,50px) scale(1.1); } }

.hero-grid {
    position: relative; z-index: 5;
    display: grid;
    grid-template-columns: 1.05fr 1fr;
    align-items: center;
    gap: 30px;
}

.hero-content { text-align: start; }

.hero-badge {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 22px;
    background: var(--glass);
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 50px;
    color: #fff; font-weight: 700; font-size: .95rem;
    margin-bottom: 26px;
    box-shadow: 0 8px 32px rgba(0,0,0,.2);
    animation: fadeInDown .8s ease-out;
}

.hero-title {
    font-size: 3.6rem; font-weight: 900; color: #fff; line-height: 1.25;
    margin-bottom: 18px;
    text-shadow: 0 2px 0 #c7d2fe, 0 4px 0 #a5b4fc, 0 6px 0 #818cf8, 0 8px 0 #6366f1, 0 10px 0 #4f46e5, 0 14px 24px rgba(0,0,0,.45);
    animation: fadeInUp .8s ease-out .2s both;
}
.hero-title .highlight {
    background: linear-gradient(135deg, #818cf8 0%, #c084fc 50%, #f472b6 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    display: inline-block; text-shadow: none;
    filter: drop-shadow(0 3px 0 #4f46e5) drop-shadow(0 6px 0 #3730a3) drop-shadow(0 12px 14px rgba(0,0,0,.4));
}

.hero-subtitle {
    font-size: 1.15rem; color: #cbd5e1; margin-bottom: 34px; max-width: 540px;
    animation: fadeInUp .8s ease-out .4s both;
}

.hero-search { max-width: 600px; margin-bottom: 36px; animation: fadeInUp .8s ease-out .6s both; }

.search-form {
    display: flex; gap: 12px;
    background: rgba(255,255,255,.96);
    padding: 8px; border-radius: 20px;
    box-shadow: 0 8px 0 #c7d2fe, 0 28px 50px rgba(0,0,0,.35);
    transition: all .3s ease;
}
.search-form:focus-within { transform: translateY(-5px); box-shadow: 0 8px 0 var(--primary), 0 36px 60px rgba(99,102,241,.4); }

.search-input {
    flex: 1; min-width: 0;
    padding: 16px 22px; border: none; border-radius: 14px;
    font-size: 1.02rem; font-family: 'Cairo', sans-serif;
    background: transparent; color: var(--dark); outline: none;
}
.search-input::placeholder { color: #94a3b8; }

.btn-search {
    padding: 16px 30px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    color: #fff; border: none; border-radius: 14px;
    font-weight: 700; font-size: 1rem; font-family: 'Cairo', sans-serif; cursor: pointer;
    box-shadow: 0 6px 0 var(--primary-deep), 0 14px 24px rgba(99,102,241,.35);
    transition: transform .15s ease, box-shadow .15s ease;
}
.btn-search:hover  { transform: translateY(-2px); box-shadow: 0 8px 0 var(--primary-deep), 0 18px 30px rgba(99,102,241,.45); }
.btn-search:active { transform: translateY(5px); box-shadow: 0 1px 0 var(--primary-deep), 0 4px 8px rgba(99,102,241,.4); }

.hero-stats { display: flex; gap: 18px; flex-wrap: wrap; animation: fadeInUp .8s ease-out .8s both; }
.stat-item {
    position: relative;
    background: linear-gradient(145deg, rgba(255,255,255,.14), rgba(255,255,255,.04));
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    padding: 16px 26px; border-radius: 18px; text-align: center; min-width: 120px;
    box-shadow: 0 6px 0 rgba(255,255,255,.06), 0 20px 40px rgba(0,0,0,.3);
}
.stat-number { font-size: 2.1rem; font-weight: 900; color: #fff; display: block; line-height: 1; transform: translateZ(40px); }
.stat-label  { color: #cbd5e1; font-size: .88rem; margin-top: 6px; display: block; transform: translateZ(22px); }

/* ---- the 3D stage ---- */
.stage {
    position: relative;
    height: 600px;
    min-width: 0;
    animation: fadeIn 1.2s ease-out .3s both;
}
.stage::before { /* soft light pool under the objects */
    content: ''; position: absolute; left: 8%; right: 8%; bottom: 6%; height: 90px;
    background: radial-gradient(ellipse at center, rgba(129,140,248,.45), transparent 70%);
    filter: blur(14px);
}
#stage-canvas {
    position: absolute; inset: 0; width: 100%; height: 100%;
    display: block; cursor: grab; touch-action: pan-y;
}
#stage-canvas:active { cursor: grabbing; }

.stage-hint {
    position: absolute; bottom: 0; left: 50%; translate: -50% 0;
    padding: 8px 18px; border-radius: 40px;
    background: var(--glass); border: 1px solid var(--glass-border);
    backdrop-filter: blur(10px);
    color: #e2e8f0; font-size: .85rem; white-space: nowrap;
    transition: opacity .6s ease;
    pointer-events: none;
}
.stage.touched .stage-hint { opacity: 0; }

/* fallback when WebGL / CDN is unavailable */
.stage-fallback { display: none; position: absolute; inset: 0; place-items: center; font-size: 11rem;
    filter: drop-shadow(0 30px 30px rgba(0,0,0,.45)); animation: bob 6s ease-in-out infinite; }
.stage.no-webgl #stage-canvas, .stage.no-webgl .stage-hint { display: none; }
.stage.no-webgl .stage-fallback { display: grid; }
@keyframes bob { 0%,100% { transform: translateY(0) rotate(-4deg); } 50% { transform: translateY(-20px) rotate(4deg); } }

/* =========================================
   SECTIONS
   ========================================= */
.section { padding: 100px 0; position: relative; }
.section-title {
    font-size: 2.5rem; font-weight: 900; text-align: center; margin-bottom: 15px;
    background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.section-subtitle { text-align: center; color: #64748b; font-size: 1.1rem; margin-bottom: 60px; }

/* Categories */
.categories-section { background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%); }
.categories-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 34px; }

.category-card {
    position: relative; background: #fff; padding: 40px 30px; border-radius: 24px;
    text-align: center; text-decoration: none; color: var(--dark);
    border: 1px solid #e2e8f0;
    box-shadow: 0 8px 0 #e0e7ff, 0 24px 40px rgba(99,102,241,.1);
    display: flex; flex-direction: column; align-items: center;
}
.category-card:hover { box-shadow: 0 8px 0 #c7d2fe, 0 40px 60px rgba(99,102,241,.22); }
.category-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px;
    border-radius: 24px 24px 0 0; background: linear-gradient(90deg, var(--primary), var(--secondary));
}
.category-icon {
    display: grid; place-items: center; width: 96px; height: 96px; margin-bottom: 22px;
    font-size: 3rem; border-radius: 28px;
    background: linear-gradient(145deg, #eef2ff, #e0e7ff);
    box-shadow: 0 8px 0 #c7d2fe, 0 18px 24px rgba(99,102,241,.25);
    transform: translateZ(70px);
}
.category-card h3 { font-size: 1.4rem; font-weight: 800; margin-bottom: 12px; transform: translateZ(40px); }
.category-card p  { color: #64748b; font-size: .95rem; line-height: 1.6; margin-bottom: 20px; transform: translateZ(25px); }
.card-arrow {
    margin-top: auto; width: 44px; height: 44px; border-radius: 50%; background: #f1f5f9;
    display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.2rem;
    box-shadow: 0 4px 0 #e2e8f0; transform: translateZ(50px);
    transition: background .3s ease, color .3s ease, translate .3s ease;
}
.category-card:hover .card-arrow { background: var(--primary); color: #fff; translate: -6px 0; box-shadow: 0 4px 0 var(--primary-deep); }

/* Exams */
.recent-section { background: #fff; }
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; }
.btn-outline {
    padding: 10px 24px; border: 2px solid #e2e8f0; border-radius: 12px; color: #64748b;
    text-decoration: none; font-weight: 600; box-shadow: 0 4px 0 #e2e8f0;
    transition: transform .15s ease, box-shadow .15s ease, color .2s ease, border-color .2s ease;
}
.btn-outline:hover  { border-color: var(--primary); color: var(--primary); box-shadow: 0 6px 0 #c7d2fe; transform: translateY(-2px); }
.btn-outline:active { transform: translateY(3px); box-shadow: 0 1px 0 #c7d2fe; }

.exams-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 34px; }
.exam-card {
    position: relative; background: #fff; border-radius: 20px; padding: 30px;
    border: 1px solid #eef2ff;
    box-shadow: 0 7px 0 #eef2ff, 0 22px 36px rgba(15,23,42,.07);
    display: flex; flex-direction: column;
}
.exam-card:hover { box-shadow: 0 7px 0 #e0e7ff, 0 36px 56px rgba(15,23,42,.14); }
.exam-badge {
    display: inline-block; padding: 6px 16px;
    background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
    color: var(--primary-dark); border-radius: 8px; font-size: .8rem; font-weight: 700;
    margin-bottom: 15px; width: fit-content; box-shadow: 0 3px 0 #c7d2fe; transform: translateZ(40px);
}
.exam-card h4 { font-size: 1.15rem; font-weight: 700; margin-bottom: 20px; line-height: 1.5; color: var(--dark); flex-grow: 1; transform: translateZ(30px); }
.exam-meta { display: flex; gap: 20px; color: #64748b; font-size: .85rem; margin-bottom: 20px; padding-top: 20px; border-top: 1px solid #f1f5f9; transform: translateZ(20px); }
.exam-meta span { display: flex; align-items: center; gap: 6px; }
.btn-download {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; padding: 14px; background: var(--dark); color: #fff; text-decoration: none;
    border-radius: 12px; font-weight: 700; font-size: .95rem;
    box-shadow: 0 6px 0 #020617, 0 14px 22px rgba(15,23,42,.25);
    transform: translateZ(55px);
    transition: background .25s ease, box-shadow .15s ease, translate .15s ease;
}
.btn-download:hover  { background: var(--primary); box-shadow: 0 6px 0 var(--primary-deep), 0 16px 26px rgba(99,102,241,.4); translate: 0 -2px; }
.btn-download:active { translate: 0 5px; box-shadow: 0 1px 0 var(--primary-deep); }
.empty-state { grid-column: 1 / -1; text-align: center; padding: 80px 20px; background: #f8fafc; border-radius: 24px; border: 2px dashed #e2e8f0; color: #64748b; font-size: 1.1rem; }

/* Features */
.why-section { background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%); }
.features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 34px; }
.feature {
    position: relative; text-align: center; padding: 40px 30px; background: #fff;
    border-radius: 24px; border: 1px solid #eef2ff;
    box-shadow: 0 7px 0 #eef2ff, 0 22px 36px rgba(15,23,42,.06);
}
.feature:hover { box-shadow: 0 7px 0 #e0e7ff, 0 36px 56px rgba(15,23,42,.12); }
.feature-icon {
    display: grid; place-items: center; width: 84px; height: 84px; margin: 0 auto 22px;
    font-size: 2.4rem; border-radius: 50%;
    background: linear-gradient(145deg, #eef2ff, #ddd6fe);
    box-shadow: 0 8px 0 #c4b5fd, 0 18px 24px rgba(139,92,246,.3);
    transform: translateZ(70px);
}
.feature h4 { font-size: 1.2rem; font-weight: 800; margin-bottom: 10px; color: var(--dark); transform: translateZ(40px); }
.feature p  { color: #64748b; font-size: .95rem; transform: translateZ(22px); }

/* CTA */
.cta-section {
    padding: 100px 0; background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    position: relative; overflow: hidden;
}
.cta-section::before {
    content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,.1) 0%, transparent 60%);
    animation: rotate 30s linear infinite;
}
@keyframes rotate { to { transform: rotate(360deg); } }
.cta-box {
    background: rgba(255,255,255,.12); backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,.25); padding: 60px 40px; border-radius: 32px;
    text-align: center; position: relative; z-index: 10;
    box-shadow: 0 10px 0 rgba(255,255,255,.1), 0 40px 70px rgba(0,0,0,.28);
}
.cta-box h2 { color: #fff; font-size: 2.5rem; font-weight: 900; margin-bottom: 20px; transform: translateZ(60px); }
.cta-box p  { color: rgba(255,255,255,.92); font-size: 1.15rem; margin: 0 auto 40px; max-width: 600px; transform: translateZ(35px); }
.cta-buttons { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; transform: translateZ(60px); }
.cta-box > .btn-lg { transform: translateZ(60px); }
.btn-lg {
    padding: 16px 40px; font-size: 1.1rem; border-radius: 16px; font-weight: 700; text-decoration: none;
    display: inline-flex; align-items: center; gap: 10px;
    transition: translate .15s ease, box-shadow .15s ease, background .25s ease;
}
.btn-white { background: #fff; color: var(--primary); box-shadow: 0 7px 0 #c7d2fe, 0 18px 30px rgba(0,0,0,.2); }
.btn-white:hover  { translate: 0 -3px; box-shadow: 0 10px 0 #c7d2fe, 0 24px 36px rgba(0,0,0,.25); }
.btn-white:active { translate: 0 6px; box-shadow: 0 1px 0 #c7d2fe; }
.btn-glass { background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.35); backdrop-filter: blur(10px); box-shadow: 0 7px 0 rgba(0,0,0,.18); }
.btn-glass:hover  { background: rgba(255,255,255,.25); translate: 0 -3px; }
.btn-glass:active { translate: 0 6px; box-shadow: 0 1px 0 rgba(0,0,0,.18); }

/* Animations & responsive */
@keyframes fadeInUp   { from { opacity: 0; translate: 0 30px; }  to { opacity: 1; translate: 0 0; } }
@keyframes fadeInDown { from { opacity: 0; translate: 0 -30px; } to { opacity: 1; translate: 0 0; } }
@keyframes fadeIn     { from { opacity: 0; } to { opacity: 1; } }
:focus-visible { outline: 3px solid var(--secondary); outline-offset: 3px; }

@media (max-width: 960px) {
    .hero { padding: 90px 0 50px; min-height: auto; }
    .hero-grid { grid-template-columns: 1fr; }
    .stage { order: -1; height: 380px; }
    .hero-title { font-size: 2.6rem; }
}
@media (max-width: 768px) {
    .search-form { flex-direction: column; background: transparent; box-shadow: none; gap: 15px; }
    .search-input { background: rgba(255,255,255,.96); box-shadow: 0 6px 0 #c7d2fe, 0 10px 30px rgba(0,0,0,.15); }
    .btn-search { width: 100%; }
    .section-header { flex-direction: column; gap: 20px; text-align: center; }
    .cta-box h2 { font-size: 1.8rem; }
}
@media (hover: none) { [data-tilt] { transform: none; } }
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation: none !important; transition: none !important; }
    [data-tilt] { transform: none; }
    .reveal { opacity: 1; translate: 0 0; }
}
</style>

<!-- Hero Section -->
<section class="hero" id="hero">
    <div class="container hero-grid">
        <div class="hero-content">
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
                <div class="stat-item" data-tilt="14">
                    <span class="stat-number" data-count="5000">0</span>
                    <span class="stat-label">امتحان</span>
                </div>
                <div class="stat-item" data-tilt="14">
                    <span class="stat-number" data-count="1200">0</span>
                    <span class="stat-label">درس</span>
                </div>
                <div class="stat-item" data-tilt="14">
                    <span class="stat-number" data-count="50000">0</span>
                    <span class="stat-label">طالب</span>
                </div>
            </div>
        </div>

        <!-- Real 3D scene (Three.js): graduation cap on books + floating exam sheets -->
        <div class="stage" id="stage">
            <canvas id="stage-canvas" role="img" aria-label="مجسّم ثلاثي الأبعاد: قبعة تخرّج فوق كتب وأوراق امتحان"></canvas>
            <div class="stage-fallback" aria-hidden="true">🎓</div>
            <div class="stage-hint">🖱️ اسحب للتدوير · اضغط على القبعة لتقفز</div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section categories-section">
    <div class="container">
        <h2 class="section-title reveal">استكشف الأقسام</h2>
        <p class="section-subtitle reveal">اختر المسار المناسب لمستواك الدراسي والمهني</p>

        <div class="categories-grid">
            <a href="bac.php" class="category-card reveal" data-tilt="12">
                <div class="category-icon">🎓</div>
                <h3>البكالوريا</h3>
                <p>امتحانات وطنية وجهوية مع التصحيح النموذجي لجميع الشعب</p>
                <div class="card-arrow">←</div>
            </a>

            <a href="college.php" class="category-card reveal" data-tilt="12">
                <div class="category-icon">📚</div>
                <h3>السلك الإعدادي</h3>
                <p>دروس ملخصة وامتحانات محلية وجهوية من الأولى إلى الثالثة</p>
                <div class="card-arrow">←</div>
            </a>

            <a href="formation.php" class="category-card reveal" data-tilt="12">
                <div class="category-icon">🔧</div>
                <h3>التكوين المهني</h3>
                <p>امتحانات وتكوينات OFPPT وشهادات مهنية معترف بها</p>
                <div class="card-arrow">←</div>
            </a>

            <a href="emploi.php" class="category-card reveal" data-tilt="12">
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
                    <div class="exam-card reveal" data-tilt="8">
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
            <div class="feature reveal" data-tilt="12">
                <span class="feature-icon">🆓</span>
                <h4>مجاني بالكامل</h4>
                <p>جميع الموارد التعليمية متاحة مجاناً وبدون أي قيود لجميع التلاميذ</p>
            </div>
            <div class="feature reveal" data-tilt="12">
                <span class="feature-icon">✅</span>
                <h4>محتوى موثوق ومعتمد</h4>
                <p>امتحانات رسمية مع التصحيح النموذجي من مصادر وزارة التربية الوطنية</p>
            </div>
            <div class="feature reveal" data-tilt="12">
                <span class="feature-icon">🔄</span>
                <h4>تحديث يومي مستمر</h4>
                <p>نضيف محتوى جديد ودروساً ملخصة بشكل أسبوعي لمواكبة البرنامج</p>
            </div>
            <div class="feature reveal" data-tilt="12">
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
        <div class="cta-box reveal" data-tilt="5">
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

<!-- Three.js (WebGL). If it fails to load, the page falls back to a simple emoji. -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

<script>
// 1. Animated Counter
const counters = document.querySelectorAll('.stat-number');
const speed = 150;

const animateCounters = () => {
    counters.forEach(counter => {
        const target = +counter.getAttribute('data-count');
        const count = +counter.innerText.replace(/[^\d]/g, '');
        const inc = target / speed;
        if (count < target) {
            counter.innerText = Math.ceil(count + inc);
            setTimeout(animateCounters, 20);
        } else {
            counter.innerText = target.toLocaleString();
        }
    });
};

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

// 2. Scroll Reveal
const revealElements = document.querySelectorAll('.reveal');
const revealOnScroll = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
            setTimeout(() => entry.target.classList.add('active'), index * 100);
            revealOnScroll.unobserve(entry.target);
        }
    });
}, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });
revealElements.forEach(el => revealOnScroll.observe(el));

// 3. Card tilt (mouse only)
const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (canHover && !reduceMotion) {
    document.querySelectorAll('[data-tilt]').forEach(el => {
        const max = parseFloat(el.dataset.tilt) || 10;
        el.addEventListener('pointermove', e => {
            const r = el.getBoundingClientRect();
            const x = (e.clientX - r.left) / r.width;
            const y = (e.clientY - r.top) / r.height;
            el.style.setProperty('--ry', ((x - 0.5) * 2 * max).toFixed(2) + 'deg');
            el.style.setProperty('--rx', ((0.5 - y) * 2 * max).toFixed(2) + 'deg');
            el.style.setProperty('--mx', (x * 100).toFixed(1) + '%');
            el.style.setProperty('--my', (y * 100).toFixed(1) + '%');
            el.classList.add('tilting');
        });
        el.addEventListener('pointerleave', () => {
            el.classList.remove('tilting');
            el.style.setProperty('--rx', '0deg');
            el.style.setProperty('--ry', '0deg');
        });
    });
}

// 4. Hero 3D scene: graduation cap on a stack of books, orbiting exam sheets
(function () {
    const stage  = document.getElementById('stage');
    const canvas = document.getElementById('stage-canvas');
    if (!stage || !canvas) return;
    if (!window.THREE) { stage.classList.add('no-webgl'); return; }

    let renderer;
    try {
        renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
    } catch (e) { stage.classList.add('no-webgl'); return; }

    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.outputEncoding = THREE.sRGBEncoding;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.15;

    const scene  = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(35, 1, 0.1, 100);
    camera.position.set(0, 2.4, 12);
    camera.lookAt(0, -0.2, 0);

    // ---- lights ----
    scene.add(new THREE.HemisphereLight(0xc7d2fe, 0x1e1b4b, 0.9));
    const key = new THREE.DirectionalLight(0xffffff, 1.7);
    key.position.set(4, 9, 6);
    key.castShadow = true;
    key.shadow.mapSize.set(1024, 1024);
    Object.assign(key.shadow.camera, { left: -7, right: 7, top: 7, bottom: -7 });
    key.shadow.bias = -0.0005;
    scene.add(key);
    const pink = new THREE.PointLight(0xec4899, 1.4, 24); pink.position.set(-6, 1, 4); scene.add(pink);
    const blue = new THREE.PointLight(0x6366f1, 1.8, 24); blue.position.set(6, -1, 5); scene.add(blue);

    // ---- ground that only receives shadows ----
    const ground = new THREE.Mesh(
        new THREE.PlaneGeometry(30, 30),
        new THREE.ShadowMaterial({ opacity: 0.38 })
    );
    ground.rotation.x = -Math.PI / 2;
    ground.position.y = -2.2;
    ground.receiveShadow = true;
    scene.add(ground);

    const rig = new THREE.Group();   // everything the user can rotate
    scene.add(rig);

    // ---- helpers ----
    function shadowed(mesh) { mesh.castShadow = true; mesh.receiveShadow = true; return mesh; }

    function book(w, h, d, color) {
        const g = new THREE.Group();
        const cover = shadowed(new THREE.Mesh(
            new THREE.BoxGeometry(w, h, d),
            new THREE.MeshStandardMaterial({ color, roughness: 0.5, metalness: 0.08 })
        ));
        const pages = shadowed(new THREE.Mesh(
            new THREE.BoxGeometry(w * 0.96, h * 0.72, d * 0.96),
            new THREE.MeshStandardMaterial({ color: 0xf5f0e6, roughness: 0.92 })
        ));
        pages.position.z = 0.09;      // pages peek out of the front edge
        g.add(cover, pages);
        return g;
    }

    // ---- books ----
    const b1 = book(4.0, 0.5, 3.0, 0x4f46e5); b1.position.set(0, -1.95, 0);    b1.rotation.y = 0.05;
    const b2 = book(3.6, 0.45, 2.7, 0xec4899); b2.position.set(0.1, -1.47, 0); b2.rotation.y = 0.2;
    const b3 = book(3.2, 0.4, 2.4, 0x8b5cf6);  b3.position.set(-0.05, -1.05, 0); b3.rotation.y = -0.12;
    rig.add(b1, b2, b3);

    // ---- graduation cap ----
    const cap = new THREE.Group();
    const capMat = new THREE.MeshPhysicalMaterial({ color: 0x1e1b4b, roughness: 0.45, clearcoat: 0.6, clearcoatRoughness: 0.35 });
    const base = shadowed(new THREE.Mesh(new THREE.CylinderGeometry(0.95, 1.02, 0.75, 48), capMat));
    base.position.y = -0.3;
    const board = shadowed(new THREE.Mesh(new THREE.BoxGeometry(2.7, 0.1, 2.7), capMat));
    board.position.y = 0.1;
    const knob = shadowed(new THREE.Mesh(new THREE.SphereGeometry(0.1, 24, 24),
        new THREE.MeshStandardMaterial({ color: 0xfbbf24, metalness: 0.8, roughness: 0.25 })));
    knob.position.y = 0.2;

    const goldMat = new THREE.MeshStandardMaterial({ color: 0xfbbf24, metalness: 0.7, roughness: 0.3 });
    const cord = shadowed(new THREE.Mesh(
        new THREE.TubeGeometry(new THREE.CatmullRomCurve3([
            new THREE.Vector3(0, 0.2, 0),
            new THREE.Vector3(0.7, 0.24, 0.7),
            new THREE.Vector3(1.3, 0.16, 1.3),
            new THREE.Vector3(1.42, -0.25, 1.42)
        ]), 40, 0.035, 8, false), goldMat));
    const tassel = shadowed(new THREE.Mesh(new THREE.CylinderGeometry(0.08, 0.15, 0.55, 16), goldMat));
    tassel.position.set(1.42, -0.55, 1.42);
    cap.add(base, board, knob, cord, tassel);
    cap.position.set(0, -0.55, 0);
    cap.rotation.y = Math.PI / 4;
    rig.add(cap);

    // ---- exam sheets (canvas texture) ----
    function sheetTexture(accent) {
        const c = document.createElement('canvas'); c.width = 256; c.height = 360;
        const x = c.getContext('2d');
        x.fillStyle = '#ffffff'; x.fillRect(0, 0, 256, 360);
        x.fillStyle = accent;    x.fillRect(0, 0, 256, 54);
        x.fillStyle = 'rgba(255,255,255,.85)'; x.fillRect(150, 18, 88, 18);
        for (let i = 0; i < 12; i++) {
            x.fillStyle = i % 4 === 0 ? '#94a3b8' : '#e2e8f0';
            const w = i % 4 === 0 ? 150 : 200;
            x.fillRect(256 - 24 - w, 84 + i * 22, w, 8);
        }
        x.strokeStyle = '#22c55e'; x.lineWidth = 6; x.lineCap = 'round';
        x.beginPath(); x.moveTo(36, 316); x.lineTo(52, 332); x.lineTo(82, 300); x.stroke();
        const t = new THREE.CanvasTexture(c);
        t.encoding = THREE.sRGBEncoding;
        t.anisotropy = 4;
        return t;
    }
    const accents = ['#6366f1', '#ec4899', '#8b5cf6', '#0ea5e9'];
    const sheets = accents.map((a, i) => {
        const m = shadowed(new THREE.Mesh(
            new THREE.BoxGeometry(1.0, 1.4, 0.03),
            new THREE.MeshStandardMaterial({ map: sheetTexture(a), roughness: 0.85 })
        ));
        m.userData = { angle: (i / accents.length) * Math.PI * 2, r: 3.5 + (i % 2) * 0.45, y: 0.4 + i * 0.35, spd: 0.0035 + i * 0.0006 };
        rig.add(m);
        return m;
    });

    // ---- sparkles ----
    const N = 70, pos = new Float32Array(N * 3);
    for (let i = 0; i < N; i++) {
        pos[i*3]   = (Math.random() - 0.5) * 11;
        pos[i*3+1] = (Math.random() - 0.4) * 6;
        pos[i*3+2] = (Math.random() - 0.5) * 8;
    }
    const sparkGeo = new THREE.BufferGeometry();
    sparkGeo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    const sparks = new THREE.Points(sparkGeo, new THREE.PointsMaterial({
        color: 0xc7d2fe, size: 0.06, transparent: true, opacity: 0.8, depthWrite: false, blending: THREE.AdditiveBlending
    }));
    rig.add(sparks);

    // ---- interaction ----
    const S = { rotY: -0.45, rotX: 0.1, velY: 0, hoverX: 0, hoverY: 0, drag: false, lx: 0, ly: 0, moved: 0,
                hop: 0, hopV: 0, spinV: 0 };
    const ray = new THREE.Raycaster();
    const ndc = new THREE.Vector2();

    function markTouched() { stage.classList.add('touched'); }

    canvas.addEventListener('pointerdown', e => {
        S.drag = true; S.lx = e.clientX; S.ly = e.clientY; S.moved = 0;
        canvas.setPointerCapture(e.pointerId);
    });
    canvas.addEventListener('pointermove', e => {
        const r = canvas.getBoundingClientRect();
        S.hoverX = ((e.clientX - r.left) / r.width - 0.5) * 2;
        S.hoverY = ((e.clientY - r.top) / r.height - 0.5) * 2;
        if (!S.drag) return;
        const dx = e.clientX - S.lx, dy = e.clientY - S.ly;
        S.lx = e.clientX; S.ly = e.clientY;
        S.moved += Math.abs(dx) + Math.abs(dy);
        S.rotY += dx * 0.009;
        S.velY = dx * 0.009;
        S.rotX = Math.max(-0.3, Math.min(0.55, S.rotX + dy * 0.004));
        if (S.moved > 4) markTouched();
    });
    function release(e) {
        if (!S.drag) return;
        S.drag = false;
        if (S.moved < 5) {                      // a click, not a drag
            const r = canvas.getBoundingClientRect();
            ndc.set(((e.clientX - r.left) / r.width) * 2 - 1, -((e.clientY - r.top) / r.height) * 2 + 1);
            ray.setFromCamera(ndc, camera);
            if (ray.intersectObject(cap, true).length) {
                S.hopV = 0.2; S.spinV = 0.4; markTouched();
            }
        }
    }
    canvas.addEventListener('pointerup', release);
    canvas.addEventListener('pointercancel', () => { S.drag = false; });
    canvas.addEventListener('pointerleave', () => { S.hoverX = 0; S.hoverY = 0; });

    // ---- sizing ----
    function resize() {
        const w = stage.clientWidth, h = stage.clientHeight;
        if (!w || !h) return;
        renderer.setSize(w, h, false);
        camera.aspect = w / h;
        const fit = 6.2 / (Math.tan(THREE.MathUtils.degToRad(camera.fov / 2)) * camera.aspect);
        camera.position.z = Math.max(12, fit);
        camera.updateProjectionMatrix();
    }
    new ResizeObserver(resize).observe(stage);
    resize();

    // ---- loop (paused when off-screen) ----
    let visible = true;
    new IntersectionObserver(es => { visible = es[0].isIntersecting; }).observe(stage);
    const clock = new THREE.Clock();

    function frame() {
        requestAnimationFrame(frame);
        if (!visible || document.hidden) return;
        const t = clock.getElapsedTime();

        if (!S.drag) {
            S.rotY += S.velY;
            S.velY *= 0.94;
            if (!reduceMotion) S.rotY += 0.0035;
        }
        rig.rotation.y = S.rotY;
        rig.rotation.x += ((S.rotX + S.hoverY * 0.1) - rig.rotation.x) * 0.08;
        rig.position.x += ((S.hoverX * 0.25) - rig.position.x) * 0.06;

        // cap: gentle float + click hop
        S.hopV -= 0.012; S.hop += S.hopV;
        if (S.hop < 0) { S.hop = 0; S.hopV = 0; }
        S.spinV *= 0.95;
        cap.position.y = -0.55 + (reduceMotion ? 0 : Math.sin(t * 1.3) * 0.1) + S.hop;
        cap.rotation.y += S.spinV;

        sheets.forEach((s, i) => {
            const u = s.userData;
            if (!reduceMotion) u.angle += u.spd;
            s.position.set(Math.cos(u.angle) * u.r, u.y + (reduceMotion ? 0 : Math.sin(t * 0.9 + i) * 0.2), Math.sin(u.angle) * u.r);
            s.rotation.y = -u.angle + Math.PI / 2;
            s.rotation.z = Math.sin(t * 0.7 + i) * 0.12;
        });
        if (!reduceMotion) sparks.rotation.y = t * 0.03;

        renderer.render(scene, camera);
    }
    frame();
})();
</script>

<?php require_once 'includes/footer.php'; ?>