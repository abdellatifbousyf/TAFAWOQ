<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'الرجاء ملء جميع الحقول.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['role'] === 'admin') {
                $_SESSION['admin'] = $user['id'];
                $_SESSION['admin_name'] = $user['username'];
                header('Location: admin/dashboard.php');
            } else {
                $_SESSION['user'] = $user['id'];
                $_SESSION['user_name'] = $user['username'];
                header('Location: user/dashboard.php');
            }
            exit;
        } else {
            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة.';
        }
    }
}

require_once 'includes/header.php';
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap');

.login-page {
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
.login-page *, .login-page *::before, .login-page *::after { box-sizing: border-box; }

.login-page::before, .login-page::after {
    content: ''; position: absolute; border-radius: 50%;
    filter: blur(90px); opacity: .5;
    animation: lpBlob 10s infinite alternate ease-in-out;
}
.login-page::before { width: 460px; height: 460px; background: var(--primary); top: -120px; left: -100px; }
.login-page::after  { width: 360px; height: 360px; background: var(--secondary); bottom: -80px; right: -60px; animation-delay: -5s; }
@keyframes lpBlob { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(30px,50px) scale(1.1); } }

.login-page .container { position: relative; z-index: 5; max-width: 1100px; margin: 0 auto; padding: 0 20px; width: 100%; }

.login-page .auth-container {
    display: grid;
    grid-template-columns: minmax(340px, 460px) 1fr;
    align-items: center;
    gap: 40px;
    max-width: none;
    margin: 0;
}

/* ---------- 3D card ---------- */
.login-page .auth-card {
    --rx: 0deg; --ry: 0deg; --mx: 50%; --my: 50%;
    position: relative;
    background: #fff;
    border-radius: 28px;
    padding: 42px 38px 34px;
    width: 100%;
    border: 1px solid rgba(255,255,255,.6);
    box-shadow: 0 10px 0 #c7d2fe, 0 40px 70px rgba(0,0,0,.45);
    transform: perspective(1000px) rotateX(var(--rx)) rotateY(var(--ry));
    transform-style: preserve-3d;
    transition: transform .6s cubic-bezier(.2,.8,.2,1);
    animation: lpIn .9s cubic-bezier(.2,.8,.2,1) both;
}
.login-page .auth-card.tilting { transition: transform .12s ease-out; }
.login-page .auth-card::after {
    content: ''; position: absolute; inset: 0; border-radius: inherit; pointer-events: none;
    background: radial-gradient(circle at var(--mx) var(--my), rgba(99,102,241,.16), transparent 55%);
    opacity: 0; transition: opacity .3s ease;
}
.login-page .auth-card.tilting::after { opacity: 1; }
.login-page .auth-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 6px;
    border-radius: 28px 28px 0 0;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
}
@keyframes lpIn { from { opacity: 0; translate: 0 40px; } to { opacity: 1; translate: 0 0; } }

.login-page .auth-header { text-align: center; margin-bottom: 28px; transform: translateZ(50px); }
.login-page .auth-header h2 {
    font-size: 2rem; font-weight: 900; margin: 0 0 6px;
    background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.login-page .auth-header p { color: #64748b; margin: 0; font-size: 1rem; }

.login-page .form-group { margin-bottom: 20px; transform: translateZ(30px); }
.login-page .form-group label { display: block; font-weight: 700; font-size: .92rem; color: var(--dark); margin-bottom: 8px; }
.login-page .form-group input {
    width: 100%;
    padding: 15px 18px;
    font-family: 'Cairo', sans-serif; font-size: 1rem; color: var(--dark);
    background: #f1f5f9;
    border: 2px solid transparent;
    border-radius: 14px;
    box-shadow: inset 0 3px 6px rgba(15,23,42,.08);
    outline: none;
    transition: background .25s ease, border-color .25s ease, box-shadow .25s ease, translate .25s ease;
}
.login-page .form-group input::placeholder { color: #94a3b8; }
.login-page .form-group input:focus {
    background: #fff;
    border-color: var(--primary);
    box-shadow: 0 6px 0 #c7d2fe, 0 16px 26px rgba(99,102,241,.22);
    translate: 0 -3px;
}

.login-page .btn-primary {
    display: block; width: 100%;
    margin-top: 8px; padding: 16px;
    font-family: 'Cairo', sans-serif; font-size: 1.08rem; font-weight: 800; color: #fff;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    border: none; border-radius: 16px; cursor: pointer;
    box-shadow: 0 7px 0 var(--primary-deep), 0 18px 28px rgba(99,102,241,.35);
    transform: translateZ(55px);
    transition: translate .15s ease, box-shadow .15s ease, filter .2s ease;
}
.login-page .btn-primary:hover  { translate: 0 -2px; box-shadow: 0 9px 0 var(--primary-deep), 0 22px 34px rgba(99,102,241,.45); filter: brightness(1.06); }
.login-page .btn-primary:active { translate: 0 6px; box-shadow: 0 1px 0 var(--primary-deep), 0 4px 10px rgba(99,102,241,.4); }

.login-page .auth-footer { text-align: center; margin-top: 26px; padding-top: 20px; border-top: 1px solid #eef2ff; transform: translateZ(25px); }
.login-page .auth-footer p { margin: 0; color: #64748b; }
.login-page .auth-footer a { color: var(--primary-dark); font-weight: 800; text-decoration: none; border-bottom: 2px solid #c7d2fe; transition: border-color .2s ease, color .2s ease; }
.login-page .auth-footer a:hover { color: var(--secondary); border-color: var(--secondary); }

.login-page .alert-danger {
    margin-bottom: 22px; padding: 14px 18px; border-radius: 14px;
    background: #fef2f2; color: #b91c1c; font-weight: 700; font-size: .95rem;
    border: 1px solid #fecaca;
    box-shadow: 0 4px 0 #fecaca;
    transform: translateZ(40px);
}
.login-page .auth-card.shake { animation: lpIn .9s both, lpShake .5s .6s both; }
@keyframes lpShake {
    0%,100% { translate: 0 0; } 20% { translate: -10px 0; } 40% { translate: 10px 0; } 60% { translate: -6px 0; } 80% { translate: 6px 0; }
}

/* ---------- 3D stage ---------- */
.login-page .login-stage { position: relative; height: 520px; min-width: 0; animation: lpFade 1.2s ease-out .3s both; }
.login-page .login-stage::before {
    content: ''; position: absolute; left: 12%; right: 12%; bottom: 8%; height: 80px;
    background: radial-gradient(ellipse at center, rgba(129,140,248,.45), transparent 70%); filter: blur(14px);
}
.login-page #lock-canvas { position: absolute; inset: 0; width: 100%; height: 100%; display: block; cursor: pointer; }
.login-page .stage-hint {
    position: absolute; bottom: 0; left: 50%; translate: -50% 0;
    padding: 8px 18px; border-radius: 40px; white-space: nowrap;
    background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.18);
    backdrop-filter: blur(10px); color: #e2e8f0; font-size: .85rem; pointer-events: none;
    transition: opacity .6s ease;
}
.login-page .login-stage.touched .stage-hint { opacity: 0; }
.login-page .stage-fallback { display: none; position: absolute; inset: 0; place-items: center; font-size: 10rem; filter: drop-shadow(0 30px 30px rgba(0,0,0,.45)); animation: lpBob 6s ease-in-out infinite; }
.login-page .login-stage.no-webgl #lock-canvas, .login-page .login-stage.no-webgl .stage-hint { display: none; }
.login-page .login-stage.no-webgl .stage-fallback { display: grid; }
@keyframes lpBob { 0%,100% { transform: translateY(0) rotate(-4deg); } 50% { transform: translateY(-18px) rotate(4deg); } }
@keyframes lpFade { from { opacity: 0; } to { opacity: 1; } }

.login-page :focus-visible { outline: 3px solid var(--secondary); outline-offset: 3px; }

@media (max-width: 960px) {
    .login-page { padding: 90px 0 50px; min-height: auto; }
    .login-page .auth-container { grid-template-columns: 1fr; justify-items: center; gap: 10px; }
    .login-page .login-stage { order: -1; height: 260px; width: 100%; }
}
@media (hover: none) { .login-page .auth-card { transform: none; } }
@media (prefers-reduced-motion: reduce) {
    .login-page *, .login-page *::before, .login-page *::after { animation: none !important; transition: none !important; }
    .login-page .auth-card { transform: none; }
}
</style>

<section class="section login-page">
    <div class="container">
        <div class="auth-container">
            <div class="auth-card<?= $error ? ' shake' : '' ?>" id="auth-card">
                <div class="auth-header">
                    <h2>🔐 تسجيل الدخول</h2>
                    <p>مرحباً بعودتك! سجل دخولك للمتابعة</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" required dir="ltr"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>كلمة المرور</label>
                        <input type="password" name="password"  required >
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">دخول</button>
                </form>

                <div class="auth-footer">
                    <p>ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a></p>
                </div>
            </div>

            <!-- Real 3D padlock (Three.js) -->
            <div class="login-stage" id="login-stage">
                <canvas id="lock-canvas" role="img" aria-label="قفل ثلاثي الأبعاد يفتح عند كتابة بيانات الدخول"></canvas>
                <div class="stage-fallback" aria-hidden="true">🔐</div>
                <div class="stage-hint">🔒 اضغط على القفل لفتحه</div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const hasError = <?= $error ? 'true' : 'false' ?>;

// Card tilt (mouse only, gentle so typing stays comfortable)
(function () {
    const card = document.getElementById('auth-card');
    if (!card || !canHover || reduceMotion) return;
    const max = 5;
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

// 3D padlock
(function () {
    const stage = document.getElementById('login-stage');
    const canvas = document.getElementById('lock-canvas');
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
    camera.position.set(0, 1.2, 11);
    camera.lookAt(0, 0, 0);

    // lights
    scene.add(new THREE.HemisphereLight(0xc7d2fe, 0x1e1b4b, 1.0));
    const key = new THREE.DirectionalLight(0xffffff, 1.8);
    key.position.set(4, 8, 7); key.castShadow = true;
    key.shadow.mapSize.set(1024, 1024);
    Object.assign(key.shadow.camera, { left: -6, right: 6, top: 6, bottom: -6 });
    key.shadow.bias = -0.0005;
    scene.add(key);
    const pink = new THREE.PointLight(0xec4899, 1.5, 24); pink.position.set(-6, 1, 5); scene.add(pink);
    const blue = new THREE.PointLight(0x6366f1, 1.8, 24); blue.position.set(6, -1, 5); scene.add(blue);

    const ground = new THREE.Mesh(new THREE.PlaneGeometry(30, 30), new THREE.ShadowMaterial({ opacity: 0.38 }));
    ground.rotation.x = -Math.PI / 2; ground.position.y = -2.15; ground.receiveShadow = true;
    scene.add(ground);

    const lock = new THREE.Group();
    lock.position.y = -0.5;
    scene.add(lock);

    const sh = m => { m.castShadow = true; m.receiveShadow = true; return m; };

    // rounded body
    const w = 2.5, h = 2.1, r = 0.4, s = new THREE.Shape();
    s.moveTo(-w/2 + r, -h/2); s.lineTo(w/2 - r, -h/2); s.quadraticCurveTo(w/2, -h/2, w/2, -h/2 + r);
    s.lineTo(w/2, h/2 - r);   s.quadraticCurveTo(w/2, h/2, w/2 - r, h/2);
    s.lineTo(-w/2 + r, h/2);  s.quadraticCurveTo(-w/2, h/2, -w/2, h/2 - r);
    s.lineTo(-w/2, -h/2 + r); s.quadraticCurveTo(-w/2, -h/2, -w/2 + r, -h/2);
    const bodyGeo = new THREE.ExtrudeGeometry(s, { depth: 0.8, bevelEnabled: true, bevelThickness: 0.12, bevelSize: 0.12, bevelSegments: 6, curveSegments: 16 });
    bodyGeo.translate(0, 0, -0.4);
    const body = sh(new THREE.Mesh(bodyGeo, new THREE.MeshPhysicalMaterial({
        color: 0x5b5bf0, roughness: 0.35, metalness: 0.25, clearcoat: 0.7, clearcoatRoughness: 0.25
    })));
    lock.add(body);

    // gold plate + keyhole
    const gold = new THREE.MeshStandardMaterial({ color: 0xfbbf24, metalness: 0.65, roughness: 0.3 });
    const plate = sh(new THREE.Mesh(new THREE.CylinderGeometry(0.42, 0.42, 0.06, 48), gold));
    plate.rotation.x = Math.PI / 2; plate.position.set(0, -0.05, 0.54);
    const dark = new THREE.MeshStandardMaterial({ color: 0x0f172a, roughness: 0.6 });
    const hole = new THREE.Mesh(new THREE.CylinderGeometry(0.13, 0.13, 0.07, 24), dark);
    hole.rotation.x = Math.PI / 2; hole.position.set(0, 0.03, 0.58);
    const slot = new THREE.Mesh(new THREE.BoxGeometry(0.1, 0.34, 0.07), dark);
    slot.position.set(0, -0.17, 0.58);
    lock.add(plate, hole, slot);

    // shackle: pivots around the left leg so it lifts and swings open
    const steel = new THREE.MeshStandardMaterial({ color: 0xe2e8f0, metalness: 0.55, roughness: 0.28 });
    const pivot = new THREE.Group();
    pivot.position.set(-0.7, 1.0, 0);
    const legGeo = new THREE.CylinderGeometry(0.17, 0.17, 0.7, 24);
    const legL = sh(new THREE.Mesh(legGeo, steel)); legL.position.set(0, 0.35, 0);
    const legR = sh(new THREE.Mesh(legGeo, steel)); legR.position.set(1.4, 0.35, 0);
    const arc  = sh(new THREE.Mesh(new THREE.TorusGeometry(0.7, 0.17, 20, 48, Math.PI), steel));
    arc.position.set(0.7, 0.7, 0);
    pivot.add(legL, legR, arc);
    lock.add(pivot);

    // sparkles
    const N = 60, pos = new Float32Array(N * 3);
    for (let i = 0; i < N; i++) {
        pos[i*3] = (Math.random() - 0.5) * 9; pos[i*3+1] = (Math.random() - 0.4) * 6; pos[i*3+2] = (Math.random() - 0.5) * 6;
    }
    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    const sparks = new THREE.Points(geo, new THREE.PointsMaterial({
        color: 0xc7d2fe, size: 0.06, transparent: true, opacity: 0.8, depthWrite: false, blending: THREE.AdditiveBlending
    }));
    scene.add(sparks);

    // state
    const S = { tx: 0, ty: 0, open: 0, forced: false, focus: 0, shake: hasError ? 1 : 0 };
    const ray = new THREE.Raycaster(), ndc = new THREE.Vector2();

    window.addEventListener('pointermove', e => {
        S.tx = (e.clientX / window.innerWidth - 0.5) * 2;
        S.ty = (e.clientY / window.innerHeight - 0.5) * 2;
    });
    document.querySelectorAll('.auth-form input').forEach(inp => {
        inp.addEventListener('focus', () => S.focus++);
        inp.addEventListener('blur',  () => S.focus = Math.max(0, S.focus - 1));
    });
    canvas.addEventListener('click', e => {
        const rc = canvas.getBoundingClientRect();
        ndc.set(((e.clientX - rc.left) / rc.width) * 2 - 1, -((e.clientY - rc.top) / rc.height) * 2 + 1);
        ray.setFromCamera(ndc, camera);
        if (ray.intersectObject(lock, true).length) {
            S.forced = !S.forced;
            stage.classList.add('touched');
        }
    });

    function resize() {
        const W = stage.clientWidth, H = stage.clientHeight;
        if (!W || !H) return;
        renderer.setSize(W, H, false);
        camera.aspect = W / H;
        const fit = 4.4 / (Math.tan(THREE.MathUtils.degToRad(camera.fov / 2)) * camera.aspect);
        camera.position.z = Math.max(11, fit);
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

        // follows the cursor across the whole page
        lock.rotation.y += ((S.tx * 0.55) - lock.rotation.y) * 0.07;
        lock.rotation.x += ((S.ty * 0.3) - lock.rotation.x) * 0.07;

        // float + error shake
        lock.position.y = -0.5 + (reduceMotion ? 0 : Math.sin(t * 1.3) * 0.12);
        S.shake *= 0.96;
        lock.rotation.z = Math.sin(t * 38) * S.shake * 0.12;

        // shackle opens while a field is focused or after a click
        const target = (S.forced || S.focus > 0) ? 1 : 0;
        S.open += (target - S.open) * 0.09;
        pivot.position.y = 1.0 + S.open * 0.45;
        pivot.rotation.y = S.open * 2.6;

        if (!reduceMotion) sparks.rotation.y = t * 0.04;
        renderer.render(scene, camera);
    }
    frame();
})();
</script>

<?php require_once 'includes/footer.php'; ?>