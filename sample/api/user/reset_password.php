<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/../../config/db.php');

$input = json_decode(file_get_contents('php://input'), true);
$token = trim($input['token'] ?? '');
$password = trim($input['password'] ?? '');

if (empty($token) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Missing token or password."]);
    exit;
}

// validate password strength
if (strlen($password) < 8) {
    echo json_encode(["success" => false, "message" => "Password must be at least 8 characters."]);
    exit;
}

// find token (we stored hash)
$token_hash = hash('sha256', $token);
$q = $conn->prepare("
  SELECT pr.user_id, pr.expires_at, u.email
  FROM password_resets pr
  JOIN users u ON pr.user_id = u.id
  WHERE pr.token_hash = ? AND pr.expires_at > NOW()
  LIMIT 1
");
$q->bind_param("s", $token_hash);
$q->execute();
$res = $q->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Invalid or expired token."]);
    exit;
}

$row = $res->fetch_assoc();
$user_id = $row['user_id'];

// update password
$newHash = password_hash($password, PASSWORD_DEFAULT);
$upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$upd->bind_param("si", $newHash, $user_id);
$upd->execute();

// ✅ تحديث حقل password_changed_at (هنا!)
$updatePwdTime = $conn->prepare("UPDATE users SET password_changed_at = NOW() WHERE id = ?");
$updatePwdTime->bind_param("i", $user_id);
$updatePwdTime->execute();


// delete all reset tokens for this user
$del = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
$del->bind_param("i", $user_id);
$del->execute();

echo json_encode(["success" => true, "message" => "Password updated. You can now log in."]);
exit;
