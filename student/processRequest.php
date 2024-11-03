<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['rollno'])) {
    header("Location: studentLogin.php");
    exit();
}

date_default_timezone_set('Asia/Kolkata');

$rollNo = $_SESSION['rollno'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get additional form data
    $accHolderName = $_POST['accHolderName'] ?? '';
    $bankName = $_POST['bankName'] ?? '';
    $accountNo = $_POST['accountNo'] ?? '';
    $ifscCode = $_POST['ifscCode'] ?? '';

    // Validate account number
    if (!preg_match('/^\d{1,19}$/', $accountNo)) { // Adjusted for BIGINT
        $_SESSION['error_message'] = "Invalid account number. It must be a numeric value up to 19 digits.";
        header("Location: noDuesRequest.php");
        exit();
    }

    // Initialize file variables
    $file = $_FILES['file'] ?? null;
    $fileError = $file['error'] ?? 0;
    $fileName = $file['name'] ?? '';
    $fileTmpName = $file['tmp_name'] ?? '';
    $fileSize = $file['size'] ?? 0;
    $allowed = ['pdf'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($fileName) {
        // Validate file type and size
        if (in_array($fileExt, $allowed)) {
            if ($fileError === 0) {
                if ($fileSize <= 2 * 1024 * 1024) { // 2MB max size
                    $fileNameNew = $rollNo . '_' . $fileName;
                    $fileDestination = '../admin/uploadFile/' . $fileNameNew;

                    if (move_uploaded_file($fileTmpName, $fileDestination)) {
                        $checkChequeQuery = "SELECT * FROM uploadcheque WHERE rollNo = ?";
                        $stmt = $conn->prepare($checkChequeQuery);
                        $stmt->bind_param("s", $rollNo);
                        $stmt->execute();
                        $chequeResult = $stmt->get_result();

                        if ($chequeResult->num_rows > 0) {
                            $updateChequeQuery = "UPDATE uploadcheque SET filePath = ?, accHolderName = ?, bankName = ?, accountNo = ?, ifscCode = ? WHERE rollNo = ?";
                            $stmt = $conn->prepare($updateChequeQuery);
                            $stmt->bind_param("ssssss", $fileDestination, $accHolderName, $bankName, $accountNo, $ifscCode, $rollNo);
                            $stmt->execute();

                            if ($stmt->affected_rows > 0) {
                                $_SESSION['success_message'] = "Cheque updated successfully!";
                            } else {
                                $_SESSION['error_message'] = "No changes were made to the cheque information.";
                            }
                        } else {
                            $insertChequeQuery = "INSERT INTO uploadcheque (rollNo, filePath, accHolderName, bankName, accountNo, ifscCode) VALUES (?, ?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($insertChequeQuery);
                            $stmt->bind_param("ssssss", $rollNo, $fileDestination, $accHolderName, $bankName, $accountNo, $ifscCode);
                            $stmt->execute();

                            if ($stmt->affected_rows > 0) {
                                $checkRequestQuery = "SELECT * FROM refundrequest WHERE rollNo = ?";
                                $stmt = $conn->prepare($checkRequestQuery);
                                $stmt->bind_param("s", $rollNo);
                                $stmt->execute();
                                $requestResult = $stmt->get_result();

                                if ($requestResult->num_rows == 0) {
                                    $requestId = 'REQ' . $rollNo;
                                    $requestDate = date('Y-m-d H:i:s');  

                                    $insertRefundQuery = "INSERT INTO refundrequest (requestId, rollNo, requestDate) VALUES (?, ?, ?)";
                                    $stmt = $conn->prepare($insertRefundQuery);
                                    $stmt->bind_param("sss", $requestId, $rollNo, $requestDate);
                                    $stmt->execute();

                                    // Insert into nodues table for each department
                                    $deptQuery = "SELECT deptId FROM department";
                                    $deptResult = $conn->query($deptQuery);

                                    while ($deptRow = $deptResult->fetch_assoc()) {
                                        $deptId = $deptRow['deptId'];
                                        $noDueApproval = 'No';
                                        $approvalDate = null;

                                        $noDuesQuery = "INSERT INTO nodues (requestId, deptId, noDueApproval, noDueComment, approvalDate) VALUES (?, ?, ?, NULL, ?)";
                                        $noDuesStmt = $conn->prepare($noDuesQuery);
                                        $noDuesStmt->bind_param("ssss", $requestId, $deptId, $noDueApproval, $approvalDate);
                                        $noDuesStmt->execute();
                                    }
                                }

                                $_SESSION['success_message'] = "Request submitted successfully!";
                            } else {
                                $_SESSION['error_message'] = "Error inserting cheque information.";
                            }
                        }
                    } else {
                        $_SESSION['error_message'] = "Error uploading the file.";
                    }
                } else {
                    $_SESSION['error_message'] = "File size exceeds 2MB limit.";
                }
            } else {
                $_SESSION['error_message'] = "Error uploading the file.";
            }
        } else {
            $_SESSION['error_message'] = "Invalid file type. Only PDF is allowed.";
        }
    } else {
        // Update only if no new file is uploaded
        $checkChequeQuery = "SELECT * FROM uploadcheque WHERE rollNo = ?";
        $stmt = $conn->prepare($checkChequeQuery);
        $stmt->bind_param("s", $rollNo);
        $stmt->execute();
        $chequeResult = $stmt->get_result();

        if ($chequeResult->num_rows > 0) {
            // Update existing cheque record with new data (if no file is uploaded)
            $updateChequeQuery = "UPDATE uploadcheque SET accHolderName = ?, bankName = ?, accountNo = ?, ifscCode = ? WHERE rollNo = ?";
            $stmt = $conn->prepare($updateChequeQuery);
            $stmt->bind_param("sssss", $accHolderName, $bankName, $accountNo, $ifscCode, $rollNo);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $_SESSION['success_message'] = "Account details updated successfully!";
            } else {
                $_SESSION['error_message'] = "No changes were made to the details.";
            }
        } else {
            $_SESSION['error_message'] = "No cheque information found to update.";
        }
    }
} else {
    $_SESSION['error_message'] = "No form data received.";
}

header("Location: noDuesRequest.php");
exit();
