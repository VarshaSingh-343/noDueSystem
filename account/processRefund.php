<?php
session_start();
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requestId = $_POST['requestId'];
    $refundDescription = $_POST['refundDescription'];
    $refundDate = date('Y-m-d H:i:s');

    // Update the refundrequest table
    $updateQuery = "UPDATE refundrequest 
                    SET refundDate = ?, refundDescription = ?, refundStatus = 'Yes'
                    WHERE requestId = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sss", $refundDate, $refundDescription, $requestId);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Refund initiated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to initiate refund.";
    }

    $stmt->close();
    $conn->close();

    header("Location: accountDashboard.php");
    exit();
}
?>
