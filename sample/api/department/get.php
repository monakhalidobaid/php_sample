<?php
header('Content-Type: application/json');
include_once '../../config/db.php'; // اتأكد إنه المسار صح
include_once '../../config/secure_page.php'; // للـ session والصلاحيات

// استقبل البحث إذا موجود
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// استقبل بيانات الباجينغ
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5; // الافتراضي 5 عناصر في الصفحة
$offset = ($page - 1) * $limit;

try {
    if ($q !== '') {
        // استعلام البيانات
        $stmt = $conn->prepare("SELECT dept_id, dept_name, status 
                                FROM departments 
                                WHERE dept_name LIKE ? 
                                ORDER BY dept_id DESC 
                                LIMIT ? OFFSET ?");
        $searchTerm = "%$q%";
        $stmt->bind_param("sii", $searchTerm, $limit, $offset);

        // استعلام العدد الكلي
        $countStmt = $conn->prepare("SELECT COUNT(*) as total 
                                     FROM departments 
                                     WHERE dept_name LIKE ?");
        $countStmt->bind_param("s", $searchTerm);
    } else {
        $stmt = $conn->prepare("SELECT dept_id, dept_name, status 
                                FROM departments 
                                ORDER BY dept_id DESC 
                                LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);

        $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM departments");
    }

    // نفذ الاستعلامات
    $stmt->execute();
    $result = $stmt->get_result();

    $departments = [];
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }

    $countStmt->execute();
    $countRes = $countStmt->get_result();
    $total = $countRes->fetch_assoc()['total'];

    echo json_encode([
        'success' => true,
        'departments' => $departments,
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
