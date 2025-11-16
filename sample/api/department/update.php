<?php

header('Content-Type: application/json; charset=utf-8');
require_once '../../config/db.php';
require_once '../../config/secure_page.php';


// 🔒 تحقق من نوع المستخدم
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied. Admins only.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? intval($input['id']) : 0;
$name = isset($input['name']) ? trim($input['name']) : '';
$status = isset($input['status']) ? trim($input['status']) : '';

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}
if ($name === '') {
    echo json_encode(['success' => false, 'message' => 'Name required']);
    exit;
}

$allowed = ['active','disabled'];
if (!in_array($status, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// 🔹 تفعيل الاستثناءات للـ mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $stmt = $conn->prepare("UPDATE departments SET dept_name = ?, status = ? WHERE dept_id = ?");
    $stmt->bind_param('ssi', $name, $status, $id);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Department updated successfully']);
    
    $stmt->close();
    $conn->close();

} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) { // Duplicate entry
        echo json_encode(['success' => false, 'message' => 'Department name already exists']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()]);
    }
}
?>
