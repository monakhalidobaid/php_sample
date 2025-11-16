<?php
require_once '../../config/db.php';
require_once '../../config/secure_page.php';
header('Content-Type: application/json');

// استقبل JSON
$input = json_decode(file_get_contents('php://input'), true);

// تحقق من البيانات
if (
    !isset($input['id'], $input['name'], $input['uid'], $input['usertype'], $input['email'], $input['phone']) ||
    empty(trim($input['id'])) ||
    empty(trim($input['name'])) ||
    empty(trim($input['uid'])) ||
    empty(trim($input['usertype'])) ||
    empty(trim($input['email'])) ||
    empty(trim($input['phone']))
) {
    echo json_encode(["success" => false, "message" => "Missing or invalid data"]);
    exit;
}

$id       = intval($input['id']);
$username = mysqli_real_escape_string($conn, trim($input['name']));
$uid      = mysqli_real_escape_string($conn, trim($input['uid']));
$usertype = mysqli_real_escape_string($conn, trim($input['usertype']));
$email    = mysqli_real_escape_string($conn, trim($input['email']));
$phone    = mysqli_real_escape_string($conn, trim($input['phone']));

// تحقق إذا UID أو Email مستخدمين من قبل غير هذا المستخدم
$checkSql = "SELECT id FROM users WHERE (uid = '$uid' OR email = '$email') AND id != $id";
$checkRes = mysqli_query($conn, $checkSql);

if (mysqli_num_rows($checkRes) > 0) {
    echo json_encode(["success" => false, "message" => "UID or Email already exists"]);
    exit;
}

// نفذ عملية التحديث
$updateSql = "UPDATE users 
              SET user_name='$username', uid='$uid', user_type='$usertype', email='$email', phone='$phone'
              WHERE id=$id";

if (mysqli_query($conn, $updateSql)) {
    echo json_encode(["success" => true, "message" => "User updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Update failed: " . mysqli_error($conn)]);
}
