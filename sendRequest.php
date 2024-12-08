<?php include 'dbconnection.php'; ?>
<?php
$requestId = $_POST['id'] ?? '';
$pageName = $_POST['page'] ?? '';
$Status = $_POST['status'] ?? '';

if ($requestId && $pageName == "StudentRequests") {
    // Prepare and execute the update query
    $sql = "UPDATE OutpassRequests
            SET CurrentLevel = CASE 
                WHEN CurrentLevel = 'Caretaker' THEN 'Warden'
                WHEN CurrentLevel = 'Warden' THEN 'HOD'
                WHEN CurrentLevel = 'HOD' THEN 'Director'
                ELSE 'Director' 
            END,
            EscalationStatus = 'Yes'
            WHERE RequestID = ? AND RequestStatus = 'Rejected'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $requestId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to escalate request']);
    }

    $stmt->close();
    $conn->close();
} 
else if ($requestId && $pageName == "AdminDashboard"){
    // Prepare and execute the update query
    $sql = "UPDATE OutpassRequests
    SET RequestStatus = ?
    WHERE RequestID = ? AND RequestStatus = 'Pending'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $Status, $requestId);

    if ($stmt->execute()) {
    echo json_encode(['success' => true]);
    } else {
    echo json_encode(['success' => false, 'message' => 'Failed to escalate request']);
    }

    $stmt->close();
    $conn->close();
}
else {
    echo json_encode(['success' => false, 'message' => 'Invalid Request ID']);
}
?>
