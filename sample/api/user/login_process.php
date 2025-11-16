<?php
session_start();
header('Content-Type: application/json');

require_once(__DIR__ . '/../../config/db.php');

$input = json_decode(file_get_contents("php://input"), true);

$uid = $input["uid"] ?? "";
$password = $input["password"] ?? "";

if (empty($uid) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Please enter both UID and Password"]);
    exit;
}

if (!preg_match("/^[a-zA-Z0-9]+$/", $uid)) {
    echo json_encode(["success" => false, "message" => "UID can only contain letters and numbers"]);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(["success" => false, "message" => "Password must be at least 8 characters long"]);
    exit;
}

// استعلام المستخدم
$sql = "SELECT * FROM users WHERE uid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit;
}

$user = $result->fetch_assoc();

// التحقق من كلمة المرور
if (password_verify($password, $user["password"])) {
    // تجديد معرف الجلسة بعد نجاح تسجيل الدخول
    session_regenerate_id(true);
    $_SESSION["user_id"]   = $user["id"];
    $_SESSION["user_name"] = $user["user_name"];
    $_SESSION["user_type"] = $user["user_type"];
    
    // ✅ حفظ وقت تسجيل الدخول في الجلسة
    $_SESSION["login_time"] = time();

    // تحديث آخر تسجيل دخول
    $update = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $update->bind_param("i", $user["id"]);
    $update->execute();

    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "user_type" => $user["user_type"]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid password"]);
}