<?php
session_start();
session_destroy();
header('Location: index.php');
exit;
?><?php
// بدء الجلسة إذا لم تكن قد بدأت
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// حذف جميع بيانات الجلسة
$_SESSION = [];

// حذف ملف تعريف ارتباط الجلسة
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// إنهاء الجلسة
session_destroy();

// العودة إلى صفحة تسجيل الدخول
header('Location: /TAFAWOQ/login.php');
exit;