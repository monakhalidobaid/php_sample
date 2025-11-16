<?php
require_once '../../config/db.php';
require_once '../../config/secure_page.php';
header('Content-Type: application/json');

$sql = "SELECT dept_id, dept_name 
        FROM departments 
        WHERE status = 'active'";   // فقط الأقسام المفعلة

$result = mysqli_query($conn, $sql);

$departments = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row;
    }
    echo json_encode(["success" => true, "departments" => $departments]);
} else {
    echo json_encode(["success" => false, "message" => "Database error"]);
}
