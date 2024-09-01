<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['rollno'])) {
    header("Location: studentLogin.php");
    exit();
}

// Set timezone to Indian Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');

$rollNo = $_SESSION['rollno'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    // Validate file type and size
    $file = $_FILES['file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    $allowed = ['pdf'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize <= 2 * 1024 * 1024) { // 2MB max size
                $fileNameNew = $rollNo . '_' . $fileName;
                $fileDestination = '../admin/uploadFile/' . $fileNameNew;

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    // Insert into uploadcheque table
                    $insertChequeQuery = "INSERT INTO uploadcheque (rollNo, filePath) VALUES (?, ?)";
                    $stmt = $conn->prepare($insertChequeQuery);
                    $stmt->bind_param("ss", $rollNo, $fileDestination);
                    $stmt->execute();

                    // Check if the insertion was successful
                    if ($stmt->affected_rows > 0) {
                        // Insert into refundrequest table
                        $requestId = 'REQ' . $rollNo;
                        $requestDate = date('Y-m-d H:i:s');  // Current datetime in IST

                        $insertRefundQuery = "INSERT INTO refundrequest (requestId, rollNo, requestDate) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($insertRefundQuery);
                        $stmt->bind_param("sss", $requestId, $rollNo, $requestDate);
                        $stmt->execute();

                        $approvalDate = null;
                        $noDueApproval = 'No'; 

                        // Insert into nodues table for each department
                        $deptQuery = "SELECT deptId FROM department";
                        $deptResult = $conn->query($deptQuery);

                        while ($deptRow = $deptResult->fetch_assoc()) {
                            $deptId = $deptRow['deptId'];

                            $noDuesQuery = "INSERT INTO nodues (requestId, deptId, noDueApproval, noDueComment, approvalDate) VALUES (?, ?, ?, NULL, ?)";
                            $noDuesStmt = $conn->prepare($noDuesQuery);
                            $noDuesStmt->bind_param("ssss", $requestId, $deptId, $noDueApproval, $approvalDate);
                            $noDuesStmt->execute();
                        }

                        // Set success message and redirect
                        $_SESSION['success_message'] = "Request submitted successfully!";
                        header("Location: noDuesRequest.php");
                        exit();
                    } else {
                        $_SESSION['error_message'] = "Error saving file information to the database.";
                    }
                } else {
                    $_SESSION['error_message'] = "Error uploading the file.";
                }
            } else {
                $_SESSION['error_message'] = "File size exceeds 2MB.";
            }
        } else {
            $_SESSION['error_message'] = "Error uploading the file.";
        }
    } else {
        $_SESSION['error_message'] = "Invalid file type. Only PDF files are allowed.";
    }

    // If any error occurs, redirect back with the error message
    header("Location: noDuesRequest.php");
    exit();
}
?>
