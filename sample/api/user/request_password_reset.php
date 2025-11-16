<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/../../config/db.php');

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$uid   = trim($input['uid'] ?? '');

// validation
if (empty($email) && empty($uid)) {
    echo json_encode(["success" => false, "message" => "Please provide email or UID"]);
    exit;
}

// ✅ التحقق من صحة فورمات الإيميل
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Please enter a valid email address"]);
    exit;
}

// find user by email or uid
if (!empty($email)) {
    $stmt = $conn->prepare("SELECT id, user_name, email FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
} else {
    $stmt = $conn->prepare("SELECT id, user_name, email FROM users WHERE uid = ? LIMIT 1");
    $stmt->bind_param("s", $uid);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => true, "message" => "If an account exists for that email/UID, a reset link will be sent."]);
    exit;
}

$user = $result->fetch_assoc();

// generate secure token
$token = bin2hex(random_bytes(32));
$token_hash = hash('sha256', $token);
$expires_at = date('Y-m-d H:i:s', time() + 900);
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

// store token hash
$ins = $conn->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at, ip_address) VALUES (?, ?, ?, ?)");
$ins->bind_param("isss", $user['id'], $token_hash, $expires_at, $ip);
$ins->execute();

// build reset link 
$resetLink = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . "/inventory_manager/public/reset_password.php?token=" . $token;

// send email
$sent = false;
try {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'roley.4747@gmail.com';
    $mail->Password = 'nklhzerqqeyzaxio';
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('roley.4747@gmail.com', 'Inventory Manager');
    $mail->addAddress($user['email'], $user['user_name']);
    $mail->isHTML(true);
    $mail->Subject = 'Password reset request';
    $mail->Body = "
      <p>Hello {$user['user_name']},</p>
      <p>We received a request to reset your password. Click the link below to reset it (valid 15 minutes):</p>
      <p><a href='{$resetLink}'>Reset Password</a></p>
      <p>If you didn't request this, ignore this email.</p>
    ";
    $mail->send();
    $sent = true;
} catch (Exception $e) {
    error_log("Password reset email error: " . $e->getMessage());
}

echo json_encode(["success" => true, "message" => "If an account exists for that email/UID, a reset link will be sent."]);
exit;