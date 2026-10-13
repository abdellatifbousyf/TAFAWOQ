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
   1. GLOBAL & TYPOGRAPHY
   ========================================= */
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
    --persp: 1000px;
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
   2. 3D TILT SYSTEM (shared by all cards)
   ========================================= */
[data-tilt] {
    --rx: 0deg; --ry: 0deg; --mx: 50%; --my: 50%;
    transform: perspective(900px) rotateX(var(--rx)) rotateY(var(--ry));
    transform-style: preserve-3d;
    transition: transform .6s cubic-bezier(.2,.8,.2,1), box-shadow .4s ease;
    will-change: transform;
}
[data-tilt].tilting { transition: transform .12s ease-out, box-shadow .4s ease; }

/* moving light that follows the pointer */
[data-tilt]::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: radial-gradient(circle at var(--mx) var(--my), rgba(255,255,255,.55), transparent 55%);
    opacity: 0;
    pointer-events: none;
    transition: opacity .3s ease;
    mix-blend-mode: soft-light;
}
[data-tilt].tilting::after { opacity: 1; }

/* children that float above the card surface */
.z-1 { transform: translateZ(30px); }
.z-2 { transform: translateZ(55px); }
.z-3 { transform: translateZ(80px); }

/* scroll reveal uses the separate `translate` property so it never fights tilt */
.reveal {
    opacity: 0;
    translate: 0 50px;
    transition: opacity .8s ease, translate .8s cubic-bezier(.2,.8,.2,1);
}
.reveal.active { opacity: 1; translate: 0 0; }

/* =========================================
   3. HERO (3D scene)
   ========================================= */
.hero {
    --px: 0; --py: 0;
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
    overflow: hidden;
    padding: 100px 0;
    perspective: 1400px;
}

.hero::before, .hero::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: .55;
    animation: floatBlob 10s infinite alternate ease-in-out;
}
.hero::before { width: 500px; height: 500px; background: var(--primary); top: -100px; left: -100px; }
.hero::after  { width: 400px; height: 400px; background: var(--secondary); bottom: -50px; right: -50px; animation-delay: -5s; }

@keyframes floatBlob {
    0%   { transform: translate(0,0) scale(1); }
    100% { transform: translate(30px,50px) scale(1.1); }
}

/* perspective floor grid */
.hero-floor {
    position: absolute;
    left: -50%; right: -50%; bottom: -10%;
    height: 60%;
    background-image:
        linear-gradient(rgba(129,140,248,.35) 1px, transparent 1px),
        linear-gradient(90deg, rgba(129,140,248,.35) 1px, transparent 1px);
    background-size: 60px 60px;
    transform: rotateX(72deg);
    transform-origin: top center;
    mask-image: linear-gradient(to bottom, rgba(0,0,0,.9), transparent 85%);
    -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,.9), transparent 85%);
    animation: gridMove 6s linear infinite;
    pointer-events: none;
}
@keyframes gridMove { to { background-position: 0 60px, 60px 0; } }

/* floating 3D objects */
.hero-scene { position: absolute; inset: 0; pointer-events: none; transform-style: preserve-3d; }

.obj {
    position: absolute;
    transform-style: preserve-3d;
    transform: translate3d(calc(var(--px) * var(--depth) * 1px), calc(var(--py) * var(--depth) * 1px), 0);
    transition: transform .25s ease-out;
}

.cube {
    --s: 90px;
    width: var(--s); height: var(--s);
    transform-style: preserve-3d;
    animation: spin 16s linear infinite;
}
.cube i {
    position: absolute; inset: 0;
    border: 1px solid rgba(255,255,255,.4);
    background: linear-gradient(135deg, rgba(129,140,248,.35), rgba(236,72,153,.18));
    box-shadow: inset 0 0 20px rgba(255,255,255,.12);
}
.cube i:nth-child(1) { transform: translateZ(calc(var(--s) / 2)); }
.cube i:nth-child(2) { transform: rotateY(180deg) translateZ(calc(var(--s) / 2)); }
.cube i:nth-child(3) { transform: rotateY(90deg)  translateZ(calc(var(--s) / 2)); }
.cube i:nth-child(4) { transform: rotateY(-90deg) translateZ(calc(var(--s) / 2)); }
.cube i:nth-child(5) { transform: rotateX(90deg)  translateZ(calc(var(--s) / 2)); }
.cube i:nth-child(6) { transform: rotateX(-90deg) translateZ(calc(var(--s) / 2)); }
@keyframes spin { to { transform: rotateX(360deg) rotateY(360deg); } }

.ring {
    width: 130px; height: 130px;
    border-radius: 50%;
    border: 10px solid transparent;
    border-top-color: #f472b6;
    border-right-color: #818cf8;
    box-shadow: 0 0 30px rgba(129,140,248,.4);
    animation: ringSpin 9s linear infinite;
}
@keyframes ringSpin {
    from { transform: rotateX(65deg) rotateZ(0); }
    to   { transform: rotateX(65deg) rotateZ(360deg); }
}

.slab {
    width: 150px; height: 190px;
    border-radius: 18px;
    background: linear-gradient(145deg, rgba(255,255,255,.18), rgba(255,255,255,.04));
    border: 1px solid var(--glass-border);
    backdrop-filter: blur(6px);
    box-shadow: 0 30px 50px rgba(0,0,0,.35);
    animation: bob 7s ease-in-out infinite;
}
@keyframes bob {
    0%, 100% { transform: rotateY(-25deg) rotateX(10deg) translateY(0); }
    50%      { transform: rotateY(-25deg) rotateX(10deg) translateY(-22px); }
}

.o1 { --depth: 40; top: 14%; left: 8%; }
.o2 { --depth: -30; bottom: 18%; right: 9%; }
.o3 { --depth: 60; top: 20%; right: 12%; }
.o4 { --depth: -50; bottom: 22%; left: 10%; }
.o5 { --depth: 25; top: 48%; left: 3%; }
.o2 .cube { --s: 60px; animation-duration: 11s; }

.hero-content {
    position: relative;
    z-index: 10;
    text-align: center;
    max-width: 900px;
    transform-style: preserve-3d;
    transform: rotateY(calc(var(--px) * 5deg)) rotateX(calc(var(--py) * -4deg));
    transition: transform .25s ease-out;
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
    font-size: .95rem;
    margin-bottom: 30px;
    box-shadow: 0 8px 32px rgba(0,0,0,.2);
    transform: translateZ(40px);
    animation: fadeInDown .8s ease-out;
}

.hero-title {
    font-size: 4rem;
    font-weight: 900;
    color: #fff;
    line-height: 1.2;
    margin-bottom: 20px;
    transform: translateZ(90px);
    text-shadow: 0 2px 0 #c7d2fe, 0 4px 0 #a5b4fc, 0 6px 0 #818cf8, 0 8px 0 #6366f1, 0 10px 0 #4f46e5, 0 14px 24px rgba(0,0,0,.45);
    animation: fadeInUp .8s ease-out .2s both;
}

.hero-title .highlight {
    background: linear-gradient(135deg, #818cf8 0%, #c084fc 50%, #f472b6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: inline-block;
    text-shadow: none;
    filter: drop-shadow(0 3px 0 #4f46e5) drop-shadow(0 6px 0 #3730a3) drop-shadow(0 12px 14px rgba(0,0,0,.4));
}

.hero-subtitle {
    font-size: 1.25rem;
    color: #cbd5e1;
    margin: 0 auto 40px;
    max-width: 600px;
    transform: translateZ(50px);
    animation: fadeInUp .8s ease-out .4s both;
}

/* 3D search box */
.hero-search {
    max-width: 700px;
    margin: 0 auto 50px;
    transform: translateZ(70px);
    animation: fadeInUp .8s ease-out .6s both;
}

.search-form {
    display: flex;
    gap: 12px;
    background: rgba(255,255,255,.96);
    padding: 8px;
    border-radius: 20px;
    box-shadow: 0 8px 0 #c7d2fe, 0 28px 50px rgba(0,0,0,.35);
    transition: all .3s ease;
}
.search-form:focus-within {
    transform: translateY(-5px);
    box-shadow: 0 8px 0 var(--primary), 0 36px 60px rgba(99,102,241,.4);
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
.search-input::placeholder { color: #94a3b8; }

.btn-search {
    padding: 16px 32px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    color: #fff;
    border: none;
    border-radius: 14px;
    font-weight: 700;
    font-size: 1rem;
    font-family: 'Cairo', sans-serif;
    cursor: pointer;
    box-shadow: 0 6px 0 var(--primary-deep), 0 14px 24px rgba(99,102,241,.35);
    transition: transform .15s ease, box-shadow .15s ease;
}
.btn-search:hover { transform: translateY(-2px); box-shadow: 0 8px 0 var(--primary-deep), 0 18px 30px rgba(99,102,241,.45); }
.btn-search:active { transform: translateY(5px); box-shadow: 0 1px 0 var(--primary-deep), 0 4px 8px rgba(99,102,241,.4); }

/* Stats */
.hero-stats {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
    animation: fadeInUp .8s ease-out .8s both;
}

.stat-item {
    position: relative;
    background: linear-gradient(145deg, rgba(255,255,255,.14), rgba(255,255,255,.04));
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    padding: 20px 30px;
    border-radius: 20px;
    text-align: center;
    min-width: 140px;
    box-shadow: 0 6px 0 rgba(255,255,255,.06), 0 20px 40px rgba(0,0,0,.3);
}
.stat-number { font-size: 2.5rem; font-weight: 900; color: #fff; display: block; line-height: 1; transform: translateZ(45px); }
.stat-label  { color: #cbd5e1; font-size: .9rem; margin-top: 8px; display: block; transform: translateZ(25px); }

/* =========================================
   4. SECTIONS GENERAL
   ========================================= */
.section { padding: 100px 0; position: relative; }

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
.section-subtitle { text-align: center; color: #64748b; font-size: 1.1rem; margin-bottom: 60px; }

/* =========================================
   5. CATEGORIES (3D cards)
   ========================================= */
.categories-section { background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%); }

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 34px;
}

.category-card {
    position: relative;
    background: #fff;
    padding: 40px 30px;
    border-radius: 24px;
    text-align: center;
    text-decoration: none;
    color: var(--dark);
    border: 1px solid #e2e8f0;
    box-shadow: 0 8px 0 #e0e7ff, 0 24px 40px rgba(99,102,241,.1);
    display: flex;
    flex-direction: column;
    align-items: center;
}
.category-card:hover { box-shadow: 0 8px 0 #c7d2fe, 0 40px 60px rgba(99,102,241,.22); }

.category-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 5px;
    border-radius: 24px 24px 0 0;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
}

.category-icon {
    display: grid;
    place-items: center;
    width: 96px; height: 96px;
    margin-bottom: 22px;
    font-size: 3rem;
    border-radius: 28px;
    background: linear-gradient(145deg, #eef2ff, #e0e7ff);
    box-shadow: 0 8px 0 #c7d2fe, 0 18px 24px rgba(99,102,241,.25);
    transform: translateZ(70px);
}
.category-card h3 { font-size: 1.4rem; font-weight: 800; margin-bottom: 12px; transform: translateZ(40px); }
.category-card p  { color: #64748b; font-size: .95rem; line-height: 1.6; margin-bottom: 20px; transform: translateZ(25px); }

.card-arrow {
    margin-top: auto;
    width: 44px; height: 44px;
    border-radius: 50%;
    background: #f1f5f9;
    display: flex; align-items: center; justify-content: center;
    color: var(--primary);
    font-size: 1.2rem;
    box-shadow: 0 4px 0 #e2e8f0;
    transform: translateZ(50px);
    transition: background .3s ease, color .3s ease, translate .3s ease;
}
.category-card:hover .card-arrow { background: var(--primary); color: #fff; translate: -6px 0; box-shadow: 0 4px 0 var(--primary-deep); }

/* =========================================
   6. EXAM CARDS
   ========================================= */
.recent-section { background: #fff; }

.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; }

.btn-outline {
    padding: 10px 24px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    box-shadow: 0 4px 0 #e2e8f0;
    transition: transform .15s ease, box-shadow .15s ease, color .2s ease, border-color .2s ease;
}
.btn-outline:hover { border-color: var(--primary); color: var(--primary); box-shadow: 0 6px 0 #c7d2fe; transform: translateY(-2px); }
.btn-outline:active { transform: translateY(3px); box-shadow: 0 1px 0 #c7d2fe; }

.exams-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 34px; }

.exam-card {
    position: relative;
    background: #fff;
    border-radius: 20px;
    padding: 30px;
    border: 1px solid #eef2ff;
    box-shadow: 0 7px 0 #eef2ff, 0 22px 36px rgba(15,23,42,.07);
    display: flex;
    flex-direction: column;
}
.exam-card:hover { box-shadow: 0 7px 0 #e0e7ff, 0 36px 56px rgba(15,23,42,.14); }

.exam-badge {
    display: inline-block;
    padding: 6px 16px;
    background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
    color: var(--primary-dark);
    border-radius: 8px;
    font-size: .8rem;
    font-weight: 700;
    margin-bottom: 15px;
    width: fit-content;
    box-shadow: 0 3px 0 #c7d2fe;
    transform: translateZ(40px);
}
.exam-card h4 { font-size: 1.15rem; font-weight: 700; margin-bottom: 20px; line-height: 1.5; color: var(--dark); flex-grow: 1; transform: translateZ(30px); }

.exam-meta {
    display: flex; gap: 20px;
    color: #64748b; font-size: .85rem;
    margin-bottom: 20px; padding-top: 20px;
    border-top: 1px solid #f1f5f9;
    transform: translateZ(20px);
}
.exam-meta span { display: flex; align-items: center; gap: 6px; }

.btn-download {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%;
    padding: 14px;
    background: var(--dark);
    color: #fff;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: .95rem;
    box-shadow: 0 6px 0 #020617, 0 14px 22px rgba(15,23,42,.25);
    transform: translateZ(55px);
    transition: background .25s ease, box-shadow .15s ease, translate .15s ease;
}
.btn-download:hover { background: var(--primary); box-shadow: 0 6px 0 var(--primary-deep), 0 16px 26px rgba(99,102,241,.4); translate: 0 -2px; }
.btn-download:active { translate: 0 5px; box-shadow: 0 1px 0 var(--primary-deep); }

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
   7. FEATURES & CTA
   ========================================= */
.why-section { background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%); }

.features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 34px; }

.feature {
    position: relative;
    text-align: center;
    padding: 40px 30px;
    background: #fff;
    border-radius: 24px;
    border: 1px solid #eef2ff;
    box-shadow: 0 7px 0 #eef2ff, 0 22px 36px rgba(15,23,42,.06);
}
.feature:hover { box-shadow: 0 7px 0 #e0e7ff, 0 36px 56px rgba(15,23,42,.12); }

/* icon as a thick 3D coin */
.feature-icon {
    display: grid; place-items: center;
    width: 84px; height: 84px;
    margin: 0 auto 22px;
    font-size: 2.4rem;
    border-radius: 50%;
    background: linear-gradient(145deg, #eef2ff, #ddd6fe);
    box-shadow: 0 8px 0 #c4b5fd, 0 18px 24px rgba(139,92,246,.3);
    transform: translateZ(70px);
}
.feature h4 { font-size: 1.2rem; font-weight: 800; margin-bottom: 10px; color: var(--dark); transform: translateZ(40px); }
.feature p  { color: #64748b; font-size: .95rem; transform: translateZ(22px); }

.cta-section {
    padding: 100px 0;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    position: relative;
    overflow: hidden;
    perspective: 1200px;
}
.cta-section::before {
    content: '';
    position: absolute;
    top: -50%; left: -50%;
    width: 200%; height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,.1) 0%, transparent 60%);
    animation: rotate 30s linear infinite;
}
@keyframes rotate { to { transform: rotate(360deg); } }

.cta-ring {
    position: absolute;
    width: 220px; height: 220px;
    border-radius: 50%;
    border: 14px solid rgba(255,255,255,.18);
    animation: ringSpin 12s linear infinite;
    pointer-events: none;
}
.cta-ring.r1 { top: 8%; left: 6%; }
.cta-ring.r2 { bottom: 6%; right: 5%; width: 160px; height: 160px; animation-duration: 16s; animation-direction: reverse; }

.cta-box {
    background: rgba(255,255,255,.12);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,.25);
    padding: 60px 40px;
    border-radius: 32px;
    text-align: center;
    position: relative;
    z-index: 10;
    box-shadow: 0 10px 0 rgba(255,255,255,.1), 0 40px 70px rgba(0,0,0,.28);
}
.cta-box h2 { color: #fff; font-size: 2.5rem; font-weight: 900; margin-bottom: 20px; transform: translateZ(60px); }
.cta-box p  { color: rgba(255,255,255,.92); font-size: 1.15rem; margin: 0 auto 40px; max-width: 600px; transform: translateZ(35px); }
.cta-buttons { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; transform: translateZ(60px); }
.cta-box > .btn-lg { transform: translateZ(60px); }

.btn-lg {
    padding: 16px 40px;
    font-size: 1.1rem;
    border-radius: 16px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex; align-items: center; gap: 10px;
    transition: translate .15s ease, box-shadow .15s ease, background .25s ease;
}
.btn-white { background: #fff; color: var(--primary); box-shadow: 0 7px 0 #c7d2fe, 0 18px 30px rgba(0,0,0,.2); }
.btn-white:hover { translate: 0 -3px; box-shadow: 0 10px 0 #c7d2fe, 0 24px 36px rgba(0,0,0,.25); }
.btn-white:active { translate: 0 6px; box-shadow: 0 1px 0 #c7d2fe; }

.btn-glass { background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.35); backdrop-filter: blur(10px); box-shadow: 0 7px 0 rgba(0,0,0,.18); }
.btn-glass:hover { background: rgba(255,255,255,.25); translate: 0 -3px; }
.btn-glass:active { translate: 0 6px; box-shadow: 0 1px 0 rgba(0,0,0,.18); }

/* =========================================
   8. ANIMATIONS & RESPONSIVE
   ========================================= */
@keyframes fadeInUp   { from { opacity: 0; translate: 0 30px; }  to { opacity: 1; translate: 0 0; } }
@keyframes fadeInDown { from { opacity: 0; translate: 0 -30px; } to { opacity: 1; translate: 0 0; } }

:focus-visible { outline: 3px solid var(--secondary); outline-offset: 3px; }

@media (max-width: 900px) {
    .o3, .o4, .o5 { display: none; }
    .hero-floor { height: 45%; }
}

@media (max-width: 768px) {
    .hero-title { font-size: 2.5rem; }
    .hero-content { transform: none; }
    .search-form { flex-direction: column; background: transparent; box-shadow: none; gap: 15px; }
    .search-input { background: rgba(255,255,255,.96); box-shadow: 0 6px 0 #c7d2fe, 0 10px 30px rgba(0,0,0,.15); }
    .btn-search { width: 100%; }
    .hero-stats { gap: 15px; }
    .stat-item { min-width: 100px; padding: 15px 20px; }
    .section-header { flex-direction: column; gap: 20px; text-align: center; }
    .cta-box h2 { font-size: 1.8rem; }
    .o1, .o2 { opacity: .6; }
}

/* touch devices / reduced motion: no tilt, no spinning */
@media (hover: none) { [data-tilt] { transform: none; } }
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation: none !important; transition: none !important; }
    [data-tilt] { transform: none; }
    .reveal { opacity: 1; translate: 0 0; }
}
</style>

<!-- Hero Section -->
<section class="hero" id="hero">
    <div class="hero-floor" aria-hidden="true"></div>

    <div class="hero-scene" aria-hidden="true">
        <div class="obj o1"><div class="cube"><i></i><i></i><i></i><i></i><i></i><i></i></div></div>
        <div class="obj o2"><div class="cube"><i></i><i></i><i></i><i></i><i></i><i></i></div></div>
        <div class="obj o3"><div class="slab"></div></div>
        <div class="obj o4"><div class="ring"></div></div>
        <div class="obj o5"><div class="ring" style="width:70px;height:70px;border-width:6px"></div></div>
    </div>

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
    <div class="cta-ring r1" aria-hidden="true"></div>
    <div class="cta-ring r2" aria-hidden="true"></div>
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

// 3. 3D tilt on cards + parallax on hero (desktop with mouse only)
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

    const hero = document.getElementById('hero');
    if (hero) {
        hero.addEventListener('pointermove', e => {
            const r = hero.getBoundingClientRect();
            hero.style.setProperty('--px', (((e.clientX - r.left) / r.width) - 0.5).toFixed(3));
            hero.style.setProperty('--py', (((e.clientY - r.top) / r.height) - 0.5).toFixed(3));
        });
        hero.addEventListener('pointerleave', () => {
            hero.style.setProperty('--px', 0);
            hero.style.setProperty('--py', 0);
        });
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>