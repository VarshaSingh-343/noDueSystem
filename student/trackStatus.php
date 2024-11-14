<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['rollno'])) {
    header("Location: studentLogin.php");
    exit();
}

$rollNo = $_SESSION['rollno'];

$query = "SELECT requestId, refundStatus, refundDate, refundDescription, verifyDetails, verifyReason FROM refundrequest WHERE rollNo = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $rollNo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $requestId = $row['requestId'];
    $refundStatus = $row['refundStatus'];
    $refundDate = $row['refundDate'];
    $refundDescription = $row['refundDescription'];
    $verifyDetails = $row['verifyDetails'];
    $verifyReason = $row['verifyReason'];
} else {
    $_SESSION['error_message'] = "No request found for this roll number.";
    header("Location: noDuesRequest.php");
    exit();
}

$query = "SELECT d.deptName, n.noDueApproval, n.noDueComment, n.approvalDate , rr.verifyDetails, rr.verifyReason 
          FROM nodues n 
          JOIN department d ON n.deptId = d.deptId 
          JOIN refundrequest rr ON rr.requestId = n.requestId 
          WHERE n.requestId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $requestId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Refund Status</title>
    <link rel="stylesheet" href="../css/noDuesRequest.css">
    <style>
        /* Your custom styles */
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="header-item">
                <?php if (isset($_SESSION['rollno'])): ?>
                    <div class="welcome-message">
                        The track dues page!
                    </div>
                <?php endif; ?>
            </div>
            <?php include 'nav.php'; ?>
        </header>

        <h2>Track Refund Status</h2>

        <main>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Department Name</th>
                            <th>No Due Approval</th>
                            <th>Comment</th>
                            <th>Approval Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['deptName']); ?></td>
                                <td>
                                    <?php
                                    if ($row['noDueApproval'] === 'Yes') {
                                        echo 'Cleared';
                                    } elseif ($row['noDueApproval'] === 'No') {
                                        echo 'Not Cleared';
                                    } else {
                                        echo htmlspecialchars($row['noDueApproval']);
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['noDueComment']); ?></td>
                                <td><?php echo htmlspecialchars($row['approvalDate']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="message">No records found for your request.</div>
            <?php endif; ?>

            <?php if (!empty($refundStatus)): ?>
                <div class="refund-info">
                    <h2>Refund Details</h2>
                    <p><strong>Verify Account Details:</strong>
                        <?php if ($verifyDetails == 'Verified'): ?>
                            <span>Your Account Details are Verified</span>
                        <?php elseif ($verifyDetails == 'Not Verified'): ?>
                            <span>Your Account Details are Not Verified</span>
                    <p><strong>Reason for Not Verifed Account Details:</strong> <?php echo htmlspecialchars($verifyReason); ?></p>
                    <?php else: ?>
                        <span>Your Account Details are Not Verified Yet</span>
                <?php endif; ?>
                </p>
                <p><strong>Refund Status:</strong>
                    <?php
                    echo htmlspecialchars($refundStatus === 'Yes' ? 'Initiated' : 'Not Initiated');
                    ?>
                </p>
                <p><strong>Initiated Date:</strong> <?php echo htmlspecialchars($refundDate); ?></p>
                <p><strong>Refund Description:</strong> <?php echo htmlspecialchars($refundDescription); ?></p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>