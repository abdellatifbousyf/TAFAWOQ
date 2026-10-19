<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'الرجاء ملء جميع الحقول.';
    } elseif (strlen($password) < 6) {
        $error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.';
    } elseif ($password !== $confirm) {
        $error = 'كلمتا المرور غير متطابقتين.';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'البريد الإلكتروني مستخدم بالفعل.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            if ($stmt->execute([$username, $email, $hash])) {
                $success = 'تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.';
            } else {
                $error = 'حدث خطأ أثناء إنشاء الحساب.';
            }
        }
    }
}

require_once 'includes/header.php';
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap');

.register-page {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --primary-deep: #3730a3;
    --secondary: #ec4899;
    --accent: #8b5cf6;
    --dark: #0f172a;
    font-family: 'Cairo', sans-serif;
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    padding: 100px 0 70px;
    background: linear-gradient(135deg, #0b1024 0%, #1e1b4b 55%, #312e81 100%);
    overflow: hidden;
}
.register-page *, .register-page *::before, .register-page *::after { box-sizing: border-box; }

.register-page::before, .register-page::after {
    content: ''; position: absolute; border-radius: 50%;
    filter: blur(90px); opacity: .5;
    animation: rpBlob 10s infinite alternate ease-in-out;
}
.register-page::before { width: 460px; height: 460px; background: var(--primary); top: -120px; left: -100px; }
.register-page::after  { width: 360px; height: 360px; background: var(--secondary); bottom: -80px; right: -60px; animation-delay: -5s; }
@keyframes rpBlob { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(30px,50px) scale(1.1); } }

.register-page .container { position: relative; z-index: 5; max-width: 1100px; margin: 0 auto; padding: 0 20px; width: 100%; }

.register-page .auth-container {
    display: grid;
    grid-template-columns: minmax(340px, 470px) 1fr;
    align-items: center;
    gap: 40px;
    max-width: none;
    margin: 0;
}

/* ---------- 3D card ---------- */
.register-page .auth-card {
    --rx: 0deg; --ry: 0deg; --mx: 50%; --my: 50%;
    position: relative;
    background: #fff;
    border-radius: 28px;
    padding: 40px 38px 32px;
    width: 100%;
    border: 1px solid rgba(255,255,255,.6);
    box-shadow: 0 10px 0 #c7d2fe, 0 40px 70px rgba(0,0,0,.45);
    transform: perspective(1000px) rotateX(var(--rx)) rotateY(var(--ry));
    transform-style: preserve-3d;
    transition: transform .6s cubic-bezier(.2,.8,.2,1);
    animation: rpIn .9s cubic-bezier(.2,.8,.2,1) both;
}
.register-page .auth-card.tilting { transition: transform .12s ease-out; }
.register-page .auth-card::after {
    content: ''; position: absolute; inset: 0; border-radius: inherit; pointer-events: none;
    background: radial-gradient(circle at var(--mx) var(--my), rgba(99,102,241,.16), transparent 55%);
    opacity: 0; transition: opacity .3s ease;
}
.register-page .auth-card.tilting::after { opacity: 1; }
.register-page .auth-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 6px;
    border-radius: 28px 28px 0 0;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
}
@keyframes rpIn { from { opacity: 0; translate: 0 40px; } to { opacity: 1; translate: 0 0; } }

.register-page .auth-header { text-align: center; margin-bottom: 24px; transform: translateZ(50px); }
.register-page .auth-header h2 {
    font-size: 1.9rem; font-weight: 900; margin: 0 0 6px;
    background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.register-page .auth-header p { color: #64748b; margin: 0; font-size: 1rem; }

.register-page .form-group { margin-bottom: 17px; transform: translateZ(30px); }
.register-page .form-group label { display: block; font-weight: 700; font-size: .92rem; color: var(--dark); margin-bottom: 7px; }
.register-page .form-group input {
    width: 100%;
    padding: 14px 18px;
    font-family: 'Cairo', sans-serif; font-size: 1rem; color: var(--dark);
    background: #f1f5f9;
    border: 2px solid transparent;
    border-radius: 14px;
    box-shadow: inset 0 3px 6px rgba(15,23,42,.08);
    outline: none;
    transition: background .25s ease, border-color .25s ease, box-shadow .25s ease, translate .25s ease;
}
.register-page .form-group input::placeholder { color: #94a3b8; }
.register-page .form-group input:focus {
    background: #fff;
    border-color: var(--primary);
    box-shadow: 0 6px 0 #c7d2fe, 0 16px 26px rgba(99,102,241,.22);
    translate: 0 -3px;
}

.register-page .btn-primary {
    display: block; width: 100%;
    margin-top: 8px; padding: 16px;
    font-family: 'Cairo', sans-serif; font-size: 1.08rem; font-weight: 800; color: #fff;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    border: none; border-radius: 16px; cursor: pointer;
    box-shadow: 0 7px 0 var(--primary-deep), 0 18px 28px rgba(99,102,241,.35);
    transform: translateZ(55px);
    transition: translate .15s ease, box-shadow .15s ease, filter .2s ease;
}
.register-page .btn-primary:hover  { translate: 0 -2px; box-shadow: 0 9px 0 var(--primary-deep), 0 22px 34px rgba(99,102,241,.45); filter: brightness(1.06); }
.register-page .btn-primary:active { translate: 0 6px; box-shadow: 0 1px 0 var(--primary-deep), 0 4px 10px rgba(99,102,241,.4); }

.register-page .auth-footer { text-align: center; margin-top: 24px; padding-top: 18px; border-top: 1px solid #eef2ff; transform: translateZ(25px); }
.register-page .auth-footer p { margin: 0; color: #64748b; }
.register-page .auth-footer a { color: var(--primary-dark); font-weight: 800; text-decoration: none; border-bottom: 2px solid #c7d2fe; transition: border-color .2s ease, color .2s ease; }
.register-page .auth-footer a:hover { color: var(--secondary); border-color: var(--secondary); }

.register-page .alert { margin-bottom: 20px; padding: 14px 18px; border-radius: 14px; font-weight: 700; font-size: .95rem; transform: translateZ(40px); }
.register-page .alert-danger  { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; box-shadow: 0 4px 0 #fecaca; }
.register-page .alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; box-shadow: 0 4px 0 #bbf7d0; }

.register-page .auth-card.shake { animation: rpIn .9s both, rpShake .5s .6s both; }
@keyframes rpShake {
    0%,100% { translate: 0 0; } 20% { translate: -10px 0; } 40% { translate: 10px 0; } 60% { translate: -6px 0; } 80% { translate: 6px 0; }
}

/* ---------- 3D stage ---------- */
.register-page .reg-stage { position: relative; height: 560px; min-width: 0; animation: rpFade 1.2s ease-out .3s both; }
.register-page .reg-stage::before {
    content: ''; position: absolute; left: 10%; right: 10%; bottom: 7%; height: 80px;
    background: radial-gradient(ellipse at center, rgba(129,140,248,.45), transparent 70%); filter: blur(14px);
}
.register-page #reg-canvas { position: absolute; inset: 0; width: 100%; height: 100%; display: block; cursor: pointer; }
.register-page .stage-hint {
    position: absolute; bottom: 0; left: 50%; translate: -50% 0;
    padding: 8px 18px; border-radius: 40px; white-space: nowrap;
    background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.18);
    backdrop-filter: blur(10px); color: #e2e8f0; font-size: .85rem; pointer-events: none;
    transition: opacity .6s ease;
}
.register-page .reg-stage.touched .stage-hint { opacity: 0; }
.register-page .stage-fallback { display: none; position: absolute; inset: 0; place-items: center; font-size: 10rem; filter: drop-shadow(0 30px 30px rgba(0,0,0,.45)); animation: rpBob 6s ease-in-out infinite; }
.register-page .reg-stage.no-webgl #reg-canvas, .register-page .reg-stage.no-webgl .stage-hint { display: none; }
.register-page .reg-stage.no-webgl .stage-fallback { display: grid; }
@keyframes rpBob { 0%,100% { transform: translateY(0) rotate(-4deg); } 50% { transform: translateY(-18px) rotate(4deg); } }
@keyframes rpFade { from { opacity: 0; } to { opacity: 1; } }

.register-page :focus-visible { outline: 3px solid var(--secondary); outline-offset: 3px; }

@media (max-width: 960px) {
    .register-page { padding: 90px 0 50px; min-height: auto; }
    .register-page .auth-container { grid-template-columns: 1fr; justify-items: center; gap: 10px; }
    .register-page .reg-stage { order: -1; height: 280px; width: 100%; }
}
@media (hover: none) { .register-page .auth-card { transform: none; } }
@media (prefers-reduced-motion: reduce) {
    .register-page *, .register-page *::before, .register-page *::after { animation: none !important; transition: none !important; }
    .register-page .auth-card { transform: none; }
}
</style>

<section class="section register-page">
    <div class="container">
        <div class="auth-container">
            <div class="auth-card<?= $error ? ' shake' : '' ?>" id="auth-card">
                <div class="auth-header">
                    <h2>✨ إنشاء حساب جديد</h2>
                    <p>انضم إلى آلاف الطلاب في منصة تفوق</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label>الاسم الكامل</label>
                        <input type="text" name="username" required placeholder="أدخل اسمك الكامل"
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" required placeholder="example@email.com" dir="ltr"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>كلمة المرور</label>
                        <input type="password" name="password" required placeholder="6 أحرف على الأقل">
                    </div>
                    <div class="form-group">
                        <label>تأكيد كلمة المرور</label>
                        <input type="password" name="confirm_password" required placeholder="أعد كتابة كلمة المرور">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">إنشاء الحساب</button>
                </form>

                <div class="auth-footer">
                    <p>لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a></p>
                </div>
            </div>

            <!-- Real 3D scene (Three.js): graduation cap on books -->
            <div class="reg-stage" id="reg-stage">
                <canvas id="reg-canvas" role="img" aria-label="قبعة تخرّج ثلاثية الأبعاد فوق كتب"></canvas>
                <div class="stage-fallback" aria-hidden="true">🎓</div>
                <div class="stage-hint">🎓 اضغط على القبعة لتقفز</div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const hasError   = <?= $error ? 'true' : 'false' ?>;
const hasSuccess = <?= $success ? 'true' : 'false' ?>;

// Card tilt (mouse only, gentle so typing stays comfortable)
(function () {
    const card = document.getElementById('auth-card');
    if (!card || !canHover || reduceMotion) return;
    const max = 4;
    card.addEventListener('pointermove', e => {
        const r = card.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
        card.style.setProperty('--ry', ((x - 0.5) * 2 * max).toFixed(2) + 'deg');
        card.style.setProperty('--rx', ((0.5 - y) * 2 * max).toFixed(2) + 'deg');
        card.style.setProperty('--mx', (x * 100).toFixed(1) + '%');
        card.style.setProperty('--my', (y * 100).toFixed(1) + '%');
        card.classList.add('tilting');
    });
    card.addEventListener('pointerleave', () => {
        card.classList.remove('tilting');
        card.style.setProperty('--rx', '0deg');
        card.style.setProperty('--ry', '0deg');
    });
})();

// 3D scene
(function () {
    const stage = document.getElementById('reg-stage');
    const canvas = document.getElementById('reg-canvas');
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
    camera.position.set(0, 2.2, 12);
    camera.lookAt(0, -0.3, 0);

    scene.add(new THREE.HemisphereLight(0xc7d2fe, 0x1e1b4b, 0.95));
    const key = new THREE.DirectionalLight(0xffffff, 1.7);
    key.position.set(4, 9, 6); key.castShadow = true;
    key.shadow.mapSize.set(1024, 1024);
    Object.assign(key.shadow.camera, { left: -7, right: 7, top: 7, bottom: -7 });
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
    const b1 = book(4.0, 0.5, 3.0, 0x4f46e5);  b1.position.set(0, -1.95, 0);     b1.rotation.y = 0.05;
    const b2 = book(3.6, 0.45, 2.7, 0xec4899); b2.position.set(0.1, -1.47, 0);   b2.rotation.y = 0.2;
    const b3 = book(3.2, 0.4, 2.4, 0x8b5cf6);  b3.position.set(-0.05, -1.05, 0); b3.rotation.y = -0.12;
    rig.add(b1, b2, b3);

    // graduation cap
    const cap = new THREE.Group();
    const capMat = new THREE.MeshPhysicalMaterial({ color: 0x1e1b4b, roughness: 0.45, clearcoat: 0.6, clearcoatRoughness: 0.35 });
    const gold = new THREE.MeshStandardMaterial({ color: 0xfbbf24, metalness: 0.7, roughness: 0.3 });
    const base = sh(new THREE.Mesh(new THREE.CylinderGeometry(0.95, 1.02, 0.75, 48), capMat)); base.position.y = -0.3;
    const board = sh(new THREE.Mesh(new THREE.BoxGeometry(2.7, 0.1, 2.7), capMat)); board.position.y = 0.1;
    const knob = sh(new THREE.Mesh(new THREE.SphereGeometry(0.1, 24, 24), gold)); knob.position.y = 0.2;
    const cord = sh(new THREE.Mesh(new THREE.TubeGeometry(new THREE.CatmullRomCurve3([
        new THREE.Vector3(0, 0.2, 0), new THREE.Vector3(0.7, 0.24, 0.7),
        new THREE.Vector3(1.3, 0.16, 1.3), new THREE.Vector3(1.42, -0.25, 1.42)
    ]), 40, 0.035, 8, false), gold));
    const tassel = sh(new THREE.Mesh(new THREE.CylinderGeometry(0.08, 0.15, 0.55, 16), gold));
    tassel.position.set(1.42, -0.55, 1.42);
    cap.add(base, board, knob, cord, tassel);
    cap.position.set(0, -0.55, 0);
    cap.rotation.y = Math.PI / 4;
    rig.add(cap);

    // sparkles
    const N = 70, pos = new Float32Array(N * 3);
    for (let i = 0; i < N; i++) {
        pos[i*3] = (Math.random() - 0.5) * 11; pos[i*3+1] = (Math.random() - 0.4) * 6; pos[i*3+2] = (Math.random() - 0.5) * 8;
    }
    const sg = new THREE.BufferGeometry();
    sg.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    const sparks = new THREE.Points(sg, new THREE.PointsMaterial({
        color: 0xc7d2fe, size: 0.06, transparent: true, opacity: 0.8, depthWrite: false, blending: THREE.AdditiveBlending
    }));
    scene.add(sparks);

    // confetti (used on success)
    const palette = [0x6366f1, 0xec4899, 0x8b5cf6, 0xfbbf24, 0x22c55e, 0x38bdf8];
    const confetti = [];
    function burst() {
        for (let i = 0; i < 70; i++) {
            const m = new THREE.Mesh(new THREE.PlaneGeometry(0.14, 0.22),
                new THREE.MeshBasicMaterial({ color: palette[i % palette.length], side: THREE.DoubleSide, transparent: true }));
            m.position.set((Math.random() - 0.5) * 1.5, 0.2, (Math.random() - 0.5) * 1.5);
            m.userData = {
                v: new THREE.Vector3((Math.random() - 0.5) * 0.2, 0.14 + Math.random() * 0.16, (Math.random() - 0.5) * 0.2),
                r: new THREE.Vector3(Math.random() * 0.2, Math.random() * 0.2, Math.random() * 0.2),
                life: 1
            };
            scene.add(m); confetti.push(m);
        }
    }

    // state
    const S = { rotY: -0.45, rotX: 0.1, velY: 0, tx: 0, ty: 0, drag: false, lx: 0, ly: 0, moved: 0,
                hop: 0, hopV: 0, spinV: 0, shake: hasError ? 1 : 0, celebrate: 0 };
    const ray = new THREE.Raycaster(), ndc = new THREE.Vector2();

    function hop(power, spin) { S.hopV = Math.max(S.hopV, power); S.spinV = Math.max(S.spinV, spin); }

    // cursor follows across the page
    window.addEventListener('pointermove', e => {
        if (S.drag) return;
        S.tx = (e.clientX / window.innerWidth - 0.5) * 2;
        S.ty = (e.clientY / window.innerHeight - 0.5) * 2;
    });

    // small bounce each time something is typed
    document.querySelectorAll('.auth-form input').forEach(inp => {
        inp.addEventListener('input', () => hop(0.07, 0.03));
    });

    canvas.addEventListener('pointerdown', e => {
        S.drag = true; S.lx = e.clientX; S.ly = e.clientY; S.moved = 0;
        canvas.setPointerCapture(e.pointerId);
    });
    canvas.addEventListener('pointermove', e => {
        if (!S.drag) return;
        const dx = e.clientX - S.lx, dy = e.clientY - S.ly;
        S.lx = e.clientX; S.ly = e.clientY; S.moved += Math.abs(dx) + Math.abs(dy);
        S.rotY += dx * 0.009; S.velY = dx * 0.009;
        S.rotX = Math.max(-0.3, Math.min(0.55, S.rotX + dy * 0.004));
        if (S.moved > 4) stage.classList.add('touched');
    });
    canvas.addEventListener('pointerup', e => {
        if (!S.drag) return;
        S.drag = false;
        if (S.moved < 5) {
            const rc = canvas.getBoundingClientRect();
            ndc.set(((e.clientX - rc.left) / rc.width) * 2 - 1, -((e.clientY - rc.top) / rc.height) * 2 + 1);
            ray.setFromCamera(ndc, camera);
            if (ray.intersectObject(cap, true).length) { hop(0.2, 0.4); stage.classList.add('touched'); }
        }
    });
    canvas.addEventListener('pointercancel', () => { S.drag = false; });

    function resize() {
        const W = stage.clientWidth, H = stage.clientHeight;
        if (!W || !H) return;
        renderer.setSize(W, H, false);
        camera.aspect = W / H;
        const fit = 6.2 / (Math.tan(THREE.MathUtils.degToRad(camera.fov / 2)) * camera.aspect);
        camera.position.z = Math.max(12, fit);
        camera.updateProjectionMatrix();
    }
    new ResizeObserver(resize).observe(stage);
    resize();

    let visible = true;
    new IntersectionObserver(es => { visible = es[0].isIntersecting; }).observe(stage);
    const clock = new THREE.Clock();

    // success: the cap throws itself up and confetti bursts
    if (hasSuccess && !reduceMotion) {
        setTimeout(() => { hop(0.3, 0.5); burst(); S.celebrate = 3; }, 500);
        setTimeout(() => { hop(0.22, 0.35); burst(); }, 1400);
    }

    function frame() {
        requestAnimationFrame(frame);
        if (!visible || document.hidden) return;
        const t = clock.getElapsedTime();

        if (!S.drag) {
            S.rotY += S.velY; S.velY *= 0.94;
            if (!reduceMotion) S.rotY += 0.0025;
        }
        rig.rotation.y = S.rotY + S.tx * 0.25;
        rig.rotation.x += ((S.rotX + S.ty * 0.1) - rig.rotation.x) * 0.08;

        // cap: float + hop physics
        S.hopV -= 0.012; S.hop += S.hopV;
        if (S.hop < 0) { S.hop = 0; S.hopV = 0; }
        S.spinV *= 0.95;
        cap.position.y = -0.55 + (reduceMotion ? 0 : Math.sin(t * 1.3) * 0.1) + S.hop;
        cap.rotation.y += S.spinV;

        // error shake
        S.shake *= 0.96;
        rig.rotation.z = Math.sin(t * 38) * S.shake * 0.1;

        // confetti physics
        for (let i = confetti.length - 1; i >= 0; i--) {
            const c = confetti[i], u = c.userData;
            u.v.y -= 0.005;
            c.position.add(u.v);
            c.rotation.x += u.r.x; c.rotation.y += u.r.y; c.rotation.z += u.r.z;
            u.life -= 0.006;
            c.material.opacity = Math.max(0, u.life);
            if (u.life <= 0) { scene.remove(c); c.geometry.dispose(); c.material.dispose(); confetti.splice(i, 1); }
        }

        if (!reduceMotion) sparks.rotation.y = t * 0.04;
        renderer.render(scene, camera);
    }
    frame();
})();
</script>

<?php require_once 'includes/footer.php'; ?>