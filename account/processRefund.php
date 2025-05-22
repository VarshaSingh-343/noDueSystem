<?php
session_start();
include '../connect.php';

date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['refundDescription'])) {
        $requestId = $_POST['requestId'];
        $refundDescription = $_POST['refundDescription'];
        $refundDate = date('Y-m-d H:i:s');

        $updateQuery = "UPDATE refundrequest 
                        SET refundDate = ?, refundDescription = ?, refundStatus = 'Yes'  
                        WHERE requestId = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sss", $refundDate, $refundDescription, $requestId);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Refund initiated successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to initiate refund. Please try again.";
        }
        $stmt->close();
        header("Location: viewRefundRequests.php");
        exit();
    }

    if (isset($_POST['verifyDetails'])) {
        $verifyDetails = $_POST['verifyDetails'];
        if ($verifyDetails == 'Verified') {
            $verifyReason = ''; // Set verifyReason to an empty string
        } else {
            $verifyReason = isset($_POST['verifyReason']) ? $_POST['verifyReason'] : '';
        }
        $requestId = $_POST['requestId'];

        $updateQuery = "UPDATE refundrequest SET verifyDetails = ?, verifyReason = ? WHERE requestId = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sss", $verifyDetails, $verifyReason, $requestId);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Account Verification updated successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to update verification details. Please try again.";
        }
        $stmt->close();
        header("Location: viewRefundRequests.php");
        exit();
    }
}
