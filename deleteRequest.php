<?php include 'dbconnection.php'; ?>
<?php
$requestId = $_POST['id'] ?? '';

if ($requestId) {
    // Prepare and execute the delete query
    $sql = "DELETE FROM OutpassRequests WHERE RequestID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $requestId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete request']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Request ID']);
}
?>
