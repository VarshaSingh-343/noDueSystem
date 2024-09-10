<?php
session_start();
include '../connect.php';

date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION['deptId'])) {
    header("Location: departmentLogin.php");
    exit();
}

$deptId = $_SESSION['deptId'];

if (!isset($_POST['requestId']) || !isset($_POST['noDueApproval']) || !isset($_POST['noDueComment'])) {
    $_SESSION['error_message'] = "Missing data for the no dues process.";
    redirectToViewRefundRequests($deptId);
    exit();
}

$requestIds = $_POST['requestId'];
$noDueApprovals = $_POST['noDueApproval'];
$noDueComments = $_POST['noDueComment'];

$approvalDate = date('Y-m-d H:i:s');

$conn->begin_transaction();

try {
    foreach ($requestIds as $reqId) {
        $approval = $noDueApprovals[$reqId];
        $comment = $noDueComments[$reqId];

        // Check if an entry exists for this requestId and deptId
        $query = "SELECT * FROM nodues WHERE requestId = ? AND deptId = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            throw new Exception("SQL Prepare Error: " . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("ss", $reqId, $deptId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update existing entry
            $stmt->close(); // Close the previous statement

            $query = "UPDATE nodues SET noDueApproval = ?, noDueComment = ?, approvalDate = ? WHERE requestId = ? AND deptId = ?";
            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                throw new Exception("SQL Prepare Error: " . htmlspecialchars($conn->error));
            }

            $stmt->bind_param("sssss", $approval, $comment, $approvalDate, $reqId, $deptId);
        } else {
            // Insert new entry
            $stmt->close(); // Close the previous statement

            $query = "INSERT INTO nodues (requestId, deptId, noDueApproval, noDueComment, approvalDate) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                throw new Exception("SQL Prepare Error: " . htmlspecialchars($conn->error));
            }

            $stmt->bind_param("sssss", $reqId, $deptId, $approval, $comment, $approvalDate);
        }

        if (!$stmt->execute()) {
            throw new Exception("Failed to update or insert data for Request ID $reqId.");
        }
    }

    $conn->commit();
    $_SESSION['success_message'] = "The request of $reqId has been successfully updated.";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = $e->getMessage();
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
redirectToViewRefundRequests($deptId);
exit();

function redirectToViewRefundRequests($deptId) {
    header("Location: viewRefundRequests.php?deptId=" . urlencode($deptId));
    exit();
}
?>
