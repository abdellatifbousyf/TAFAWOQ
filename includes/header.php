<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($pdo)) {
    require_once __DIR__ . '/db.php';
}

// Base URL of the project (all links are built from it, so they work from any folder)
$base = '/TAFAWOQ';

$current_page = basename($_SERVER['PHP_SELF']);
$active = fn(string ...$files) => in_array($current_page, $files, true) ? ' active' : '';

// A page can set $page_title before including this file
$page_title = $page_title ?? 'تفوق - المنصة التعليمية المغربية';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0b1024">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="<?= $base ?>/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

    <style>
    /* =========================================
       3D NAVBAR (scoped to .navbar)
       ========================================= */
    .navbar {
        position: sticky; top: 0; z-index: 1000;
        font-family: 'Cairo', sans-serif;
        background: linear-gradient(180deg, rgba(11,16,36,.94), rgba(24,22,72,.9));
        backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
        border-bottom: 1px solid rgba(255,255,255,.1);
        box-shadow: 0 5px 0 rgba(99,102,241,.28), 0 18px 30px rgba(0,0,0,.25);
        transition: box-shadow .3s ease;
    }
    .navbar.scrolled { box-shadow: 0 5px 0 rgba(99,102,241,.4), 0 24px 40px rgba(0,0,0,.4); }
    .navbar *, .navbar *::before, .navbar *::after { box-sizing: border-box; }

    .navbar .nav-container {
        max-width: 1200px; margin: 0 auto; padding: 0 20px;
        height: 72px; display: flex; align-items: center; justify-content: space-between; gap: 20px;
    }

    /* logo */
    .navbar .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .navbar .logo-icon {
        display: grid; place-items: center; width: 44px; height: 44px; font-size: 1.35rem;
        border-radius: 14px;
        background: linear-gradient(145deg, #818cf8, #6366f1);
        box-shadow: 0 5px 0 #3730a3, 0 12px 18px rgba(99,102,241,.4);
        transition: transform .3s cubic-bezier(.2,.8,.2,1);
    }
    .navbar .logo:hover .logo-icon { transform: translateY(-3px) rotate(-8deg); }
    .navbar .logo-text {
        font-size: 1.7rem; font-weight: 900; color: #fff; line-height: 1;
        text-shadow: 0 2px 0 #6366f1, 0 4px 0 #4f46e5, 0 8px 12px rgba(0,0,0,.4);
    }

    /* links */
    .navbar .nav-links { list-style: none; margin: 0; padding: 0; display: flex; align-items: center; gap: 6px; }
    .navbar .nav-links li { margin: 0; padding: 0; list-style: none; }
    .navbar .nav-link, .navbar .nav-search-btn, .navbar .nav-user, .navbar .nav-admin {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 16px; border-radius: 12px;
        color: #cbd5e1; font-weight: 700; font-size: .95rem; text-decoration: none; white-space: nowrap;
        border: 1px solid transparent;
        transition: transform .15s ease, box-shadow .15s ease, background .2s ease, color .2s ease;
    }
    .navbar .nav-link:hover, .navbar .nav-search-btn:hover, .navbar .nav-user:hover, .navbar .nav-admin:hover {
        color: #fff; background: rgba(255,255,255,.09); transform: translateY(-2px);
        box-shadow: 0 4px 0 rgba(255,255,255,.08);
    }
    .navbar .nav-link.active {
        color: #fff; background: linear-gradient(135deg, #6366f1, #8b5cf6);
        box-shadow: 0 4px 0 #3730a3, 0 10px 16px rgba(99,102,241,.35);
    }
    .navbar .nav-search-btn { background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.16); }
    .navbar .nav-search-btn.active { background: rgba(236,72,153,.25); border-color: rgba(236,72,153,.5); color: #fff; }
    .navbar .nav-user, .navbar .nav-admin { color: #e0e7ff; }

    /* buttons inside the bar */
    .navbar .btn {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 9px 18px; border-radius: 12px; border: 2px solid transparent;
        font-family: 'Cairo', sans-serif; font-weight: 800; font-size: .92rem; text-decoration: none; cursor: pointer; white-space: nowrap;
        transition: transform .15s ease, box-shadow .15s ease, filter .2s ease;
    }
    .navbar .btn:active { transform: translateY(3px); }
    .navbar .btn-primary { color: #fff; background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 4px 0 #3730a3, 0 10px 16px rgba(99,102,241,.35); }
    .navbar .btn-primary:hover { transform: translateY(-2px); filter: brightness(1.08); }
    .navbar .btn-primary:active { box-shadow: 0 1px 0 #3730a3; }
    .navbar .btn-outline { color: #fff; background: rgba(255,255,255,.06); border-color: rgba(255,255,255,.3); box-shadow: 0 4px 0 rgba(255,255,255,.12); }
    .navbar .btn-outline:hover { transform: translateY(-2px); background: rgba(255,255,255,.14); }
    .navbar .btn-outline:active { box-shadow: 0 1px 0 rgba(255,255,255,.12); }
    .navbar .btn-danger { color: #fff; background: linear-gradient(135deg, #f43f5e, #ef4444); box-shadow: 0 4px 0 #b91c1c, 0 10px 16px rgba(239,68,68,.3); }
    .navbar .btn-danger:hover { transform: translateY(-2px); filter: brightness(1.06); }
    .navbar .btn-danger:active { box-shadow: 0 1px 0 #b91c1c; }

    /* hamburger */
    .navbar .menu-toggle {
        display: none; flex-direction: column; justify-content: center; align-items: center; gap: 5px;
        width: 46px; height: 46px; padding: 0; cursor: pointer;
        background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.2); border-radius: 14px;
        box-shadow: 0 4px 0 rgba(255,255,255,.1);
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .navbar .menu-toggle:active { transform: translateY(3px); box-shadow: 0 1px 0 rgba(255,255,255,.1); }
    .navbar .menu-toggle span { display: block; width: 22px; height: 3px; border-radius: 3px; background: #fff; transition: transform .3s ease, opacity .2s ease; }
    .navbar .menu-toggle.open span:nth-child(1) { transform: translateY(8px) rotate(45deg); }
    .navbar .menu-toggle.open span:nth-child(2) { opacity: 0; }
    .navbar .menu-toggle.open span:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }

    .navbar :focus-visible { outline: 3px solid #ec4899; outline-offset: 3px; }

    @media (max-width: 960px) {
        .navbar .menu-toggle { display: flex; }
        .navbar .nav-links {
            position: absolute; top: calc(100% + 10px); left: 14px; right: 14px;
            flex-direction: column; align-items: stretch; gap: 8px; padding: 16px;
            background: linear-gradient(180deg, #14123c, #0b1024);
            border: 1px solid rgba(255,255,255,.12); border-radius: 22px;
            box-shadow: 0 8px 0 rgba(99,102,241,.3), 0 30px 50px rgba(0,0,0,.5);
            transform-origin: top center; transform: perspective(900px) rotateX(-80deg);
            opacity: 0; visibility: hidden; pointer-events: none;
            transition: transform .45s cubic-bezier(.2,.8,.2,1), opacity .3s ease, visibility 0s .45s;
        }
        .navbar .nav-links.open {
            transform: perspective(900px) rotateX(0); opacity: 1; visibility: visible; pointer-events: auto;
            transition: transform .45s cubic-bezier(.2,.8,.2,1), opacity .3s ease;
        }
        .navbar .nav-links li > a { display: flex; width: 100%; justify-content: center; padding: 13px 16px; }
    }
    @media (prefers-reduced-motion: reduce) {
        .navbar *, .navbar *::before, .navbar *::after { transition: none !important; }
    }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="container nav-container">
        <a href="<?= $base ?>/index.php" class="logo">
            <span class="logo-icon">🏆</span>
            <span class="logo-text">تفوّق</span>
        </a>

        <button type="button" class="menu-toggle" id="menuToggle" aria-label="القائمة" aria-controls="navLinks" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <ul class="nav-links" id="navLinks">
            <li><a href="<?= $base ?>/index.php" class="nav-link<?= $active('index.php') ?>">الرئيسية</a></li>
            <li><a href="<?= $base ?>/pages/college.php" class="nav-link<?= $active('college.php') ?>">الإعدادي</a></li>
            <li><a href="<?= $base ?>/pages/bac.php" class="nav-link<?= $active('bac.php') ?>">البكالوريا</a></li>
            <li><a href="<?= $base ?>/pages/formation.php" class="nav-link<?= $active('formation.php') ?>">التكوين</a></li>
            <li><a href="<?= $base ?>/pages/emploi.php" class="nav-link<?= $active('emploi.php') ?>">التوظيف</a></li>
            <li><a href="<?= $base ?>/pages/recherche.php" class="nav-search-btn<?= $active('recherche.php') ?>">🔍 بحث</a></li>

            <?php if (isset($_SESSION['admin'])): ?>
                <li><a href="<?= $base ?>/admin/dashboard.php" class="nav-admin">⚙️ لوحة التحكم</a></li>
                <li><a href="<?= $base ?>/logout.php" class="btn btn-sm btn-danger">خروج</a></li>
            <?php elseif (isset($_SESSION['user'])): ?>
                <li><a href="<?= $base ?>/user/dashboard.php" class="nav-user">👤 <?= htmlspecialchars($_SESSION['user_name'] ?? 'حسابي') ?></a></li>
                <li><a href="<?= $base ?>/logout.php" class="btn btn-sm btn-danger">خروج</a></li>
            <?php else: ?>
                <li><a href="<?= $base ?>/login.php" class="btn btn-sm btn-outline">تسجيل الدخول</a></li>
                <li><a href="<?= $base ?>/register.php" class="btn btn-sm btn-primary">حساب جديد</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<script>
(function () {
    const toggle = document.getElementById('menuToggle');
    const links  = document.getElementById('navLinks');
    const nav    = document.querySelector('.navbar');
    if (!toggle || !links || !nav) return;

    const setOpen = open => {
        links.classList.toggle('open', open);
        toggle.classList.toggle('open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    };
    toggle.addEventListener('click', e => { e.stopPropagation(); setOpen(!links.classList.contains('open')); });
    links.addEventListener('click', e => { if (e.target.closest('a')) setOpen(false); });
    document.addEventListener('click', e => { if (!nav.contains(e.target)) setOpen(false); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') setOpen(false); });
    window.addEventListener('scroll', () => nav.classList.toggle('scrolled', window.scrollY > 8), { passive: true });
})();
</script>