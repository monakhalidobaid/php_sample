<?php
session_start();
require_once(__DIR__ . '/db.php');

$user_type = $_SESSION['user_type'] ?? 'user'; 

// مدة صلاحية الجلسة (30 دقيقة)
$timeout_duration = 60 * 30;

// تجديد معرف الجلسة كل 30 دقيقة
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} else {
    $interval = 60 * 30; // 30 دقيقة
    if (time() - $_SESSION['last_regeneration'] >= $interval) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// تحقق من أن المستخدم قام بتسجيل الدخول
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// تحقق من انتهاء مهلة الجلسة
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?session_expired=1");
    exit();
}

// ✅ التحقق من أن كلمة المرور لم تتغير بعد تسجيل الدخول
if (isset($_SESSION["login_time"]) && isset($_SESSION["user_id"])) {
    $checkStmt = $conn->prepare("SELECT password_changed_at FROM users WHERE id = ?");
    $checkStmt->bind_param("i", $_SESSION["user_id"]);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $userData = $checkResult->fetch_assoc();
        
        // إذا كان password_changed_at موجود وأحدث من login_time
        if (!empty($userData['password_changed_at'])) {
            $passwordChangedTime = strtotime($userData['password_changed_at']);
            
            if ($passwordChangedTime > $_SESSION['login_time']) {
                // كلمة المرور تغيرت بعد تسجيل الدخول - إنهاء الجلسة
                session_unset();
                session_destroy();
                header("Location: login.php?error=password_changed");
                exit;
            }
        }
    }
    $checkStmt->close();
}

// ✅ تحديث آخر نشاط
$_SESSION['last_activity'] = time();
?>