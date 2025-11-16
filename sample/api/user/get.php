<?php
header('Content-Type: application/json');
include_once '../../config/db.php';
include_once '../../config/secure_page.php';





$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$offset = ($page - 1) * $limit;

// 👇 استثناء المستخدم الحالي من النتائج
$currentUserId = $_SESSION['user_id'] ?? 0;

try {
    if ($q !== '') {
        $stmt = $conn->prepare("SELECT id, user_name, uid, user_type, email, phone 
                                FROM users 
                                WHERE (user_name LIKE ? OR uid LIKE ?) 
                                AND id != ? 
                                ORDER BY id DESC 
                                LIMIT ? OFFSET ?");
        $searchTerm = "%$q%";
        $stmt->bind_param("ssiii", $searchTerm, $searchTerm, $currentUserId, $limit, $offset);

        $countStmt = $conn->prepare("SELECT COUNT(*) as total 
                                     FROM users 
                                     WHERE (user_name LIKE ? OR uid LIKE ?) 
                                     AND id != ?");
        $countStmt->bind_param("ssi", $searchTerm, $searchTerm, $currentUserId);
    } else {
        $stmt = $conn->prepare("SELECT id, user_name, uid, user_type, email, phone 
                                FROM users 
                                WHERE id != ? 
                                ORDER BY id DESC 
                                LIMIT ? OFFSET ?");
        $stmt->bind_param("iii", $currentUserId, $limit, $offset);

        $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE id != ?");
        $countStmt->bind_param("i", $currentUserId);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    $countStmt->execute();
    $countRes = $countStmt->get_result();
    $total = $countRes->fetch_assoc()['total'];

    echo json_encode([
        'success' => true,
        'users' => $users,
        'total' => $total,
        'page' => $page,
        'limit' => $limit
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

