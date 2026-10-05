<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($pdo)) {
    require_once __DIR__ . '/db.php';
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفوق - المنصة التعليمية المغربية</title>
    <link rel="stylesheet" href="/Tafawoq/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

<nav class="navbar">
    <div class="container nav-container">
        <a href="/Tafawoq/index.php" class="logo">
            <span class="logo-icon">🏆</span>
            <span class="logo-text">تفوّق</span>
        </a>

        <button class="menu-toggle" id="menuToggle">
            <span></span><span></span><span></span>
        </button>

        <ul class="nav-links" id="navLinks">
            <li><a href="/Tafawoq/index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">الرئيسية</a></li>
            <li><a href="/Tafawoq/pages/bac.php" class="<?= $current_page == 'bac.php' ? 'active' : '' ?>">البكالوريا</a></li>
            <li><a href="/Tafawoq/pages/college.php" class="<?= $current_page == 'college.php' ? 'active' : '' ?>">الإعدادي</a></li>
            <li><a href="/Tafawoq/pages/formation.php" class="<?= $current_page == 'formation.php' ? 'active' : '' ?>">التكوين</a></li>
            <li><a href="/Tafawoq/pages/emploi.php" class="<?= $current_page == 'emploi.php' ? 'active' : '' ?>">التوظيف</a></li>
            <li><a href="/Tafawoq/pages/recherche.php" class="nav-search-btn">🔍 بحث</a></li>
            
            <?php if (isset($_SESSION['admin'])): ?>
                <li><a href="/Tafawoq/user/dashboard.php" class="nav-user">👤 حسابي</a></li>
                <li><a href="/Tafawoq/admin/dashboard.php" class="nav-admin">⚙️ لوحة التحكم</a></li>
                <li><a href="/Tafawoq/logout.php" class="btn btn-sm btn-danger">خروج</a></li>
            <?php elseif (isset($_SESSION['user'])): ?>
                <li><a href="/Tafawoq/user/dashboard.php" class="nav-user">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></a></li>
                <li><a href="/Tafawoq/logout.php" class="btn btn-sm btn-danger">خروج</a></li>
            <?php else: ?>
                <li><a href="/Tafawoq/login.php" class="btn btn-sm btn-outline">تسجيل الدخول</a></li>
                <li><a href="/Tafawoq/register.php" class="btn btn-sm btn-primary">حساب جديد</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>