<?php
require_once '../includes/header.php';

$level = $_GET['level'] ?? '';
$subject = $_GET['subject'] ?? '';

$where = "WHERE c.slug = 'college'";
$params = [];

if ($level) { $where .= " AND e.title LIKE ?"; $params[] = "%$level%"; }
if ($subject) { $where .= " AND e.title LIKE ?"; $params[] = "%$subject%"; }

$stmt = $pdo->prepare("SELECT e.*, c.name as category_name FROM exams e JOIN categories c ON e.category_id = c.id $where ORDER BY e.created_at DESC");
$stmt->execute($params);
$exams = $stmt->fetchAll();
?>
<link rel="stylesheet" href="../styles.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap');

.college-page {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --primary-deep: #3730a3;
    --secondary: #ec4899;
    --accent: #8b5cf6;
    --dark: #0f172a;
    font-family: 'Cairo', sans-serif;
    background: #f8fafc;
}
.college-page *, .college-page *::before, .college-page *::after { box-sizing: border-box; }
.college-page .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

/* ---------- 3D tilt + reveal ---------- */
.college-page [data-tilt] {
    --rx: 0deg; --ry: 0deg; --mx: 50%; --my: 50%;
    transform: perspective(900px) rotateX(var(--rx)) rotateY(var(--ry));
    transform-style: preserve-3d;
    transition: transform .6s cubic-bezier(.2,.8,.2,1), box-shadow .4s ease;
}
.college-page [data-tilt].tilting { transition: transform .12s ease-out, box-shadow .4s ease; }
.college-page [data-tilt]::after {
    content: ''; position: absolute; inset: 0; border-radius: inherit; pointer-events: none;
    background: radial-gradient(circle at var(--mx) var(--my), rgba(255,255,255,.55), transparent 55%);
    opacity: 0; transition: opacity .3s ease; mix-blend-mode: soft-light;
}
.college-page [data-tilt].tilting::after { opacity: 1; }
.college-page .reveal { opacity: 0; translate: 0 50px; transition: opacity .8s ease, translate .8s cubic-bezier(.2,.8,.2,1); }
.college-page .reveal.active { opacity: 1; translate: 0 0; }

/* ---------- Header with 3D stage ---------- */
.college-page .page-header {
    position: relative;
    padding: 110px 0 110px;
    background: linear-gradient(135deg, #0b1024 0%, #1e1b4b 55%, #312e81 100%);
    overflow: hidden;
    text-align: start;
}
.college-page .page-header::before, .college-page .page-header::after {
    content: ''; position: absolute; border-radius: 50%; filter: blur(90px); opacity: .5;
    animation: cpBlob 10s infinite alternate ease-in-out;
}
.college-page .page-header::before { width: 420px; height: 420px; background: var(--primary); top: -140px; left: -100px; }
.college-page .page-header::after  { width: 340px; height: 340px; background: var(--secondary); bottom: -120px; right: -60px; animation-delay: -5s; }
@keyframes cpBlob { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(30px,50px) scale(1.1); } }

.college-page .page-header .container {
    position: relative; z-index: 5;
    display: grid; grid-template-columns: 1.1fr 1fr; align-items: center; gap: 20px;
}
.college-page .page-header h1 {
    font-size: 3rem; font-weight: 900; color: #fff; line-height: 1.25; margin: 0 0 12px;
    text-shadow: 0 2px 0 #c7d2fe, 0 4px 0 #a5b4fc, 0 6px 0 #818cf8, 0 8px 0 #6366f1, 0 12px 22px rgba(0,0,0,.45);
    animation: cpUp .8s ease-out both;
}
.college-page .page-header p { color: #cbd5e1; font-size: 1.15rem; margin: 0 0 22px; animation: cpUp .8s ease-out .15s both; }
.college-page .breadcrumb {
    display: inline-flex; gap: 8px; align-items: center;
    padding: 8px 20px; border-radius: 40px; color: #e2e8f0; font-size: .92rem;
    background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.18);
    backdrop-filter: blur(10px); animation: cpUp .8s ease-out .3s both;
}
.college-page .breadcrumb a { color: #fff; font-weight: 700; text-decoration: none; border-bottom: 2px solid rgba(255,255,255,.3); }
.college-page .breadcrumb a:hover { border-color: var(--secondary); }
.college-page .breadcrumb span { color: #c7d2fe; }

.college-page .head-stage { position: relative; height: 300px; min-width: 0; animation: cpFade 1.2s ease-out .3s both; }
.college-page .head-stage::before {
    content: ''; position: absolute; left: 12%; right: 12%; bottom: 6%; height: 60px;
    background: radial-gradient(ellipse at center, rgba(129,140,248,.45), transparent 70%); filter: blur(12px);
}
.college-page #head-canvas { position: absolute; inset: 0; width: 100%; height: 100%; display: block; cursor: pointer; }
.college-page .stage-fallback { display: none; position: absolute; inset: 0; place-items: center; font-size: 8rem; filter: drop-shadow(0 25px 25px rgba(0,0,0,.45)); animation: cpBob 6s ease-in-out infinite; }
.college-page .head-stage.no-webgl #head-canvas { display: none; }
.college-page .head-stage.no-webgl .stage-fallback { display: grid; }
@keyframes cpBob { 0%,100% { transform: translateY(0) rotate(-4deg); } 50% { transform: translateY(-16px) rotate(4deg); } }
@keyframes cpUp { from { opacity: 0; translate: 0 30px; } to { opacity: 1; translate: 0 0; } }
@keyframes cpFade { from { opacity: 0; } to { opacity: 1; } }

/* ---------- Section + 3D filter bar (floats over the header) ---------- */
.college-page .section { padding: 0 0 90px; position: relative; z-index: 6; }

.college-page .filters-bar {
    display: flex; flex-wrap: wrap; gap: 14px; align-items: center;
    margin: -52px 0 50px; padding: 18px;
    background: #fff; border-radius: 24px; border: 1px solid #eef2ff;
    box-shadow: 0 9px 0 #e0e7ff, 0 30px 50px rgba(15,23,42,.14);
}
.college-page .filter-select {
    flex: 1 1 200px; min-width: 0;
    appearance: none; -webkit-appearance: none;
    padding: 14px 18px 14px 46px;
    font-family: 'Cairo', sans-serif; font-size: 1rem; font-weight: 600; color: var(--dark);
    background: #f1f5f9 url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>") no-repeat 16px center;
    border: 2px solid transparent; border-radius: 14px;
    box-shadow: inset 0 3px 6px rgba(15,23,42,.08);
    outline: none; cursor: pointer;
    transition: background-color .25s ease, border-color .25s ease, box-shadow .25s ease, translate .25s ease;
}
.college-page .filter-select:hover { border-color: #c7d2fe; }
.college-page .filter-select:focus { background-color: #fff; border-color: var(--primary); box-shadow: 0 6px 0 #c7d2fe, 0 14px 24px rgba(99,102,241,.2); translate: 0 -3px; }

.college-page .btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    font-family: 'Cairo', sans-serif; font-weight: 800; font-size: 1rem; text-decoration: none; cursor: pointer;
    padding: 14px 28px; border-radius: 14px; border: none;
    transition: translate .15s ease, box-shadow .15s ease, filter .2s ease, color .2s ease, border-color .2s ease;
}
.college-page .btn-primary {
    color: #fff; background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    box-shadow: 0 6px 0 var(--primary-deep), 0 14px 24px rgba(99,102,241,.35);
}
.college-page .btn-primary:hover  { translate: 0 -2px; filter: brightness(1.06); box-shadow: 0 8px 0 var(--primary-deep), 0 18px 30px rgba(99,102,241,.45); }
.college-page .btn-primary:active { translate: 0 5px; box-shadow: 0 1px 0 var(--primary-deep), 0 4px 8px rgba(99,102,241,.4); }
.college-page .btn-outline {
    color: #64748b; background: #fff; border: 2px solid #e2e8f0; box-shadow: 0 5px 0 #e2e8f0;
}
.college-page .btn-outline:hover  { color: var(--primary); border-color: var(--primary); translate: 0 -2px; box-shadow: 0 7px 0 #c7d2fe; }
.college-page .btn-outline:active { translate: 0 4px; box-shadow: 0 1px 0 #c7d2fe; }

/* ---------- Exam cards ---------- */
.college-page .exams-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 34px; }
.college-page .exam-card {
    position: relative; background: #fff; border-radius: 20px; padding: 30px;
    border: 1px solid #eef2ff; display: flex; flex-direction: column;
    box-shadow: 0 7px 0 #eef2ff, 0 22px 36px rgba(15,23,42,.07);
}
.college-page .exam-card:hover { box-shadow: 0 7px 0 #e0e7ff, 0 36px 56px rgba(15,23,42,.14); }
.college-page .exam-badge {
    display: inline-block; width: fit-content; padding: 6px 16px; margin-bottom: 15px;
    background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%); color: var(--primary-dark);
    border-radius: 8px; font-size: .8rem; font-weight: 700; box-shadow: 0 3px 0 #c7d2fe; transform: translateZ(40px);
}
.college-page .exam-card h4 { font-size: 1.15rem; font-weight: 700; margin: 0 0 20px; line-height: 1.5; color: var(--dark); flex-grow: 1; transform: translateZ(30px); }
.college-page .exam-meta { display: flex; gap: 20px; color: #64748b; font-size: .85rem; margin-bottom: 20px; padding-top: 20px; border-top: 1px solid #f1f5f9; transform: translateZ(20px); }
.college-page .exam-meta span { display: flex; align-items: center; gap: 6px; }
.college-page .btn-download {
    width: 100%; color: #fff; background: var(--dark); padding: 14px; font-size: .95rem;
    box-shadow: 0 6px 0 #020617, 0 14px 22px rgba(15,23,42,.25); transform: translateZ(55px);
}
.college-page .btn-download:hover  { background: var(--primary); translate: 0 -2px; box-shadow: 0 6px 0 var(--primary-deep), 0 16px 26px rgba(99,102,241,.4); }
.college-page .btn-download:active { translate: 0 5px; box-shadow: 0 1px 0 var(--primary-deep); }

.college-page .empty-state {
    text-align: center; padding: 80px 20px; background: #fff; border-radius: 24px;
    border: 2px dashed #cbd5e1; color: #64748b; font-size: 1.1rem; box-shadow: 0 8px 0 #eef2ff;
}

.college-page :focus-visible { outline: 3px solid var(--secondary); outline-offset: 3px; }

@media (max-width: 900px) {
    .college-page .page-header { padding: 90px 0 90px; }
    .college-page .page-header .container { grid-template-columns: 1fr; }
    .college-page .head-stage { order: -1; height: 230px; }
    .college-page .page-header h1 { font-size: 2.2rem; }
}
@media (max-width: 600px) {
    .college-page .filters-bar { margin-top: -40px; }
    .college-page .filters-bar .btn { flex: 1 1 40%; }
}
@media (hover: none) { .college-page [data-tilt] { transform: none; } }
@media (prefers-reduced-motion: reduce) {
    .college-page *, .college-page *::before, .college-page *::after { animation: none !important; transition: none !important; }
    .college-page [data-tilt] { transform: none; }
    .college-page .reveal { opacity: 1; translate: 0 0; }
}
</style>

<div class="college-page">

<div class="page-header">
    <div class="container">
        <div class="head-text">
            <h1>📚 المستوى الإعدادي</h1>
            <p>دروس و امتحانات من الأولى إلى الثالثة إعدادي</p>
            <div class="breadcrumb">
                <a href="/Tafawoq/index.php">الرئيسية</a> / <span>الإعدادي</span>
            </div>
        </div>

        <!-- Real 3D scene (Three.js): books + pencil -->
        <div class="head-stage" id="head-stage">
            <canvas id="head-canvas" role="img" aria-label="كتب وقلم رصاص بثلاثة أبعاد"></canvas>
            <div class="stage-fallback" aria-hidden="true">📚</div>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">

        <form class="filters-bar" method="GET">
            <select name="level" class="filter-select">
                <option value="">كل المستويات</option>
                <option value="الأولى" <?= $level == 'الأولى' ? 'selected' : '' ?>>الأولى إعدادي</option>
                <option value="الثانية" <?= $level == 'الثانية' ? 'selected' : '' ?>>الثانية إعدادي</option>
                <option value="الثالثة" <?= $level == 'الثالثة' ? 'selected' : '' ?>>الثالثة إعدادي</option>
            </select>

            <select name="subject" class="filter-select">
                <option value="">كل المواد</option>
                <option value="رياضيات" <?= $subject == 'رياضيات' ? 'selected' : '' ?>>رياضيات</option>
                <option value="فيزياء" <?= $subject == 'فيزياء' ? 'selected' : '' ?>>فيزياء و كيمياء</option>
                <option value="علوم الحياة" <?= $subject == 'علوم الحياة' ? 'selected' : '' ?>>علوم الحياة و الأرض</option>
                <option value="فرنسية" <?= $subject == 'فرنسية' ? 'selected' : '' ?>>اللغة الفرنسية</option>
                <option value="عربية" <?= $subject == 'عربية' ? 'selected' : '' ?>>اللغة العربية</option>
                <option value="إنجليزية" <?= $subject == 'إنجليزية' ? 'selected' : '' ?>>اللغة الإنجليزية</option>
                <option value="اجتماعيات" <?= $subject == 'اجتماعيات' ? 'selected' : '' ?>>الاجتماعيات</option>
                <option value="تربية إسلامية" <?= $subject == 'تربية إسلامية' ? 'selected' : '' ?>>التربية الإسلامية</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm">تصفية</button>
            <a href="college.php" class="btn btn-outline btn-sm">إعادة</a>
        </form>

        <div class="exams-grid">
            <?php if (empty($exams)): ?>
                <div class="empty-state reveal" style="grid-column:1/-1;">
                    <p>📭 لا توجد نتائج حالياً. سيتم إضافة المحتوى قريباً!</p>
                </div>
            <?php else:
                foreach ($exams as $exam): ?>
                    <div class="exam-card reveal" data-tilt="8">
                        <div class="exam-badge"><?= htmlspecialchars($exam['category_name']) ?></div>
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

    </div>
</section>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Scroll reveal
const revealOnScroll = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
            setTimeout(() => entry.target.classList.add('active'), index * 90);
            revealOnScroll.unobserve(entry.target);
        }
    });
}, { threshold: 0.1, rootMargin: "0px 0px -40px 0px" });
document.querySelectorAll('.college-page .reveal').forEach(el => revealOnScroll.observe(el));

// Card tilt (mouse only)
if (canHover && !reduceMotion) {
    document.querySelectorAll('.college-page [data-tilt]').forEach(el => {
        const max = parseFloat(el.dataset.tilt) || 8;
        el.addEventListener('pointermove', e => {
            const r = el.getBoundingClientRect();
            const x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
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

// Header 3D scene: stack of books + pencil
(function () {
    const stage = document.getElementById('head-stage');
    const canvas = document.getElementById('head-canvas');
    if (!stage || !canvas) return;
    if (!window.THREE) { stage.classList.add('no-webgl'); return; }

    let renderer;
    try { renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true }); }
    catch (e) { stage.classList.add('no-webgl'); return; }

    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.outputEncoding = THREE.sRGBEncoding;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.15;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(35, 1, 0.1, 100);
    camera.position.set(0, 2.4, 12);
    camera.lookAt(0, -0.5, 0);

    scene.add(new THREE.HemisphereLight(0xc7d2fe, 0x1e1b4b, 0.95));
    const key = new THREE.DirectionalLight(0xffffff, 1.7);
    key.position.set(4, 9, 6); key.castShadow = true;
    key.shadow.mapSize.set(1024, 1024);
    Object.assign(key.shadow.camera, { left: -6, right: 6, top: 6, bottom: -6 });
    key.shadow.bias = -0.0005;
    scene.add(key);
    const pink = new THREE.PointLight(0xec4899, 1.4, 24); pink.position.set(-6, 1, 4); scene.add(pink);
    const blue = new THREE.PointLight(0x6366f1, 1.8, 24); blue.position.set(6, -1, 5); scene.add(blue);

    const ground = new THREE.Mesh(new THREE.PlaneGeometry(30, 30), new THREE.ShadowMaterial({ opacity: 0.38 }));
    ground.rotation.x = -Math.PI / 2; ground.position.y = -2.2; ground.receiveShadow = true;
    scene.add(ground);

    const rig = new THREE.Group();
    scene.add(rig);
    const sh = m => { m.castShadow = true; m.receiveShadow = true; return m; };

    function book(w, h, d, color) {
        const g = new THREE.Group();
        const cover = sh(new THREE.Mesh(new THREE.BoxGeometry(w, h, d),
            new THREE.MeshStandardMaterial({ color, roughness: 0.5, metalness: 0.08 })));
        const pages = sh(new THREE.Mesh(new THREE.BoxGeometry(w * 0.96, h * 0.72, d * 0.96),
            new THREE.MeshStandardMaterial({ color: 0xf5f0e6, roughness: 0.92 })));
        pages.position.z = 0.09;
        g.add(cover, pages);
        return g;
    }
    const books = [
        book(4.0, 0.55, 3.0, 0x4f46e5),
        book(3.6, 0.5, 2.7, 0xec4899),
        book(3.2, 0.45, 2.4, 0x8b5cf6),
        book(2.7, 0.4, 2.0, 0x0ea5e9)
    ];
    const ys = [-1.92, -1.4, -0.93, -0.5];
    const rots = [0.05, 0.22, -0.14, 0.3];
    books.forEach((b, i) => { b.position.set(i % 2 ? 0.1 : -0.05, ys[i], 0); b.rotation.y = rots[i]; rig.add(b); });

    // pencil (hex body, wood tip, graphite, ferrule, eraser)
    const pencilTilt = new THREE.Group();
    const pencil = new THREE.Group();
    const bodyM = new THREE.MeshStandardMaterial({ color: 0xf59e0b, roughness: 0.45 });
    const body = sh(new THREE.Mesh(new THREE.CylinderGeometry(0.11, 0.11, 2.2, 6), bodyM));
    const wood = sh(new THREE.Mesh(new THREE.ConeGeometry(0.11, 0.35, 6), new THREE.MeshStandardMaterial({ color: 0xf1c9a0, roughness: 0.8 })));
    wood.rotation.x = Math.PI; wood.position.y = -1.275;
    const lead = sh(new THREE.Mesh(new THREE.ConeGeometry(0.045, 0.14, 6), new THREE.MeshStandardMaterial({ color: 0x1e293b, roughness: 0.5 })));
    lead.rotation.x = Math.PI; lead.position.y = -1.45;
    const ferrule = sh(new THREE.Mesh(new THREE.CylinderGeometry(0.115, 0.115, 0.22, 20), new THREE.MeshStandardMaterial({ color: 0xcbd5e1, metalness: 0.7, roughness: 0.3 })));
    ferrule.position.y = 1.21;
    const eraser = sh(new THREE.Mesh(new THREE.CylinderGeometry(0.11, 0.11, 0.2, 20), new THREE.MeshStandardMaterial({ color: 0xf472b6, roughness: 0.7 })));
    eraser.position.y = 1.42;
    pencil.add(body, wood, lead, ferrule, eraser);
    pencilTilt.add(pencil);
    pencilTilt.position.set(2.5, 0.6, 0.9);
    pencilTilt.rotation.z = -0.7;
    rig.add(pencilTilt);

    // sparkles
    const N = 60, pos = new Float32Array(N * 3);
    for (let i = 0; i < N; i++) {
        pos[i*3] = (Math.random() - 0.5) * 10; pos[i*3+1] = (Math.random() - 0.4) * 5; pos[i*3+2] = (Math.random() - 0.5) * 7;
    }
    const sg = new THREE.BufferGeometry();
    sg.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    const sparks = new THREE.Points(sg, new THREE.PointsMaterial({
        color: 0xc7d2fe, size: 0.06, transparent: true, opacity: 0.8, depthWrite: false, blending: THREE.AdditiveBlending
    }));
    scene.add(sparks);

    // state
    const S = { rotY: -0.5, velY: 0, tx: 0, ty: 0, wobble: 0 };
    const ray = new THREE.Raycaster(), ndc = new THREE.Vector2();

    window.addEventListener('pointermove', e => {
        S.tx = (e.clientX / window.innerWidth - 0.5) * 2;
        S.ty = (e.clientY / window.innerHeight - 0.5) * 2;
    });
    canvas.addEventListener('click', e => {
        const rc = canvas.getBoundingClientRect();
        ndc.set(((e.clientX - rc.left) / rc.width) * 2 - 1, -((e.clientY - rc.top) / rc.height) * 2 + 1);
        ray.setFromCamera(ndc, camera);
        if (ray.intersectObject(rig, true).length) { S.velY += 0.18; S.wobble = 1; }
    });
    // the books spin when a filter changes
    document.querySelectorAll('.filter-select').forEach(sel => {
        sel.addEventListener('change', () => { S.velY += 0.22; S.wobble = 1; });
    });

    function resize() {
        const W = stage.clientWidth, H = stage.clientHeight;
        if (!W || !H) return;
        renderer.setSize(W, H, false);
        camera.aspect = W / H;
        const fit = 5.6 / (Math.tan(THREE.MathUtils.degToRad(camera.fov / 2)) * camera.aspect);
        camera.position.z = Math.max(12, fit);
        camera.updateProjectionMatrix();
    }
    new ResizeObserver(resize).observe(stage);
    resize();

    let visible = true;
    new IntersectionObserver(es => { visible = es[0].isIntersecting; }).observe(stage);
    const clock = new THREE.Clock();

    function frame() {
        requestAnimationFrame(frame);
        if (!visible || document.hidden) return;
        const t = clock.getElapsedTime();

        S.rotY += S.velY; S.velY *= 0.94;
        if (!reduceMotion) S.rotY += 0.0045;
        rig.rotation.y = S.rotY + S.tx * 0.25;
        rig.rotation.x += ((0.08 + S.ty * 0.1) - rig.rotation.x) * 0.08;

        // books wobble a little after a click / filter change
        S.wobble *= 0.95;
        books.forEach((b, i) => {
            b.position.x = (i % 2 ? 0.1 : -0.05) + Math.sin(t * 14 + i) * S.wobble * 0.08;
        });

        // pencil floats and spins on its own axis
        if (!reduceMotion) {
            pencilTilt.position.y = 0.6 + Math.sin(t * 1.2) * 0.15;
            pencil.rotation.y = t * 0.8;
            sparks.rotation.y = t * 0.04;
        }
        renderer.render(scene, camera);
    }
    frame();
})();
</script>

<?php require_once '../includes/footer.php'; ?>