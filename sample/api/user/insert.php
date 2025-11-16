<?php
require_once '../../config/db.php';
require_once '../../config/secure_page.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "No input received"]);
    exit;
}

$username = trim($data['username'] ?? '');
$uid = trim($data['uid'] ?? '');
$password = trim($data['password'] ?? '');
$usertype = trim($data['usertype'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');

// تحقق سريع قبل الإدخال
if ($username === '' || $uid === '' || $password === '' || $usertype === '' || $email === '' || $phone === '') {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

// هاش الباسوورد
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("INSERT INTO users (user_name, uid, password, user_type, email, phone, last_login) 
                            VALUES (?, ?, ?, ?, ?, ?, NULL)");
    $stmt->bind_param("ssssss", $username, $uid, $hashedPassword, $usertype, $email, $phone);
    $stmt->execute();

    echo json_encode(["success" => true, "message" => "User added successfully"]);

} catch (mysqli_sql_exception $e) {
    // تحقق إذا كان خطأ تكرار
    if ($e->getCode() == 1062) {
        // استخراج اسم الحقل المكرر من رسالة الخطأ
        $errorMessage = $e->getMessage();
        
        if (stripos($errorMessage, 'uid') !== false) {
            echo json_encode(["success" => false, "message" => "UID already exists. Please choose another.", "field" => "uid"]);
        } elseif (stripos($errorMessage, 'email') !== false) {
            echo json_encode(["success" => false, "message" => "Email already exists. Please choose another.", "field" => "email"]);
        } else {
            echo json_encode(["success" => false, "message" => "Duplicate entry detected. Please check your data."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
    }
}