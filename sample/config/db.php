<?php
date_default_timezone_set(date_default_timezone_get());
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "inventory_manager";

// إنشاء الاتصال
$conn = new mysqli($host, $user, $pass, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    // لو الاتصال فشل، نرسل JSON ونوقف التنفيذ
    header('Content-Type: application/json'); // مهم لنجعل الرد JSON
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit; // يوقف تنفيذ باقي الصفحة
}