<?php
session_start(); // نبدأ الجلسة عشان نقدر نتحكم فيها

// مسح كل البيانات داخل الـ session
$_SESSION = [];

// تدمير الجلسة بالكامل
session_destroy();

// إعادة التوجيه لصفحة تسجيل الدخول
header("Location: ../../public/login.php");
exit;
?>
