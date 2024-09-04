<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: accountLogin.php");
    exit();
}

$username = $_SESSION['username'];

$initiatedQuery = "SELECT s.rollNo, s.name, s.course, s.batchSession, rr.requestId, rr.refundDate, rr.refundDescription
                   FROM refundrequest rr
                   JOIN student s ON rr.rollNo = s.rollNo
                   WHERE rr.refundStatus = 'Yes'";

$initiatedStmt = $conn->prepare($initiatedQuery);

if ($initiatedStmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$initiatedStmt->execute();
$initiatedResult = $initiatedStmt->get_result();

if ($initiatedResult === false) {
    die('Execute failed: ' . htmlspecialchars($initiatedStmt->error));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Initiated Refunds</title>
    <link rel="stylesheet" href="accountDashboard.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-item">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="welcome-message">
                        <h2>Initiated Refunds</h2>
                    </div>
                <?php endif; ?>
            </div>
            <?php include 'accountNav.php'; ?>
        </header>
        <main>
            <div class="table-container">
                <table id="initiatedRefundTable">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Roll Number</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Refund Date</th>
                            <th>Refund Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $initiatedResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['requestId']); ?></td>
                                <td><?php echo htmlspecialchars($row['rollNo']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['course']); ?></td>
                                <td><?php echo htmlspecialchars($row['batchSession']); ?></td>
                                <td><?php echo htmlspecialchars($row['refundDate']); ?></td>
                                <td><?php echo htmlspecialchars($row['refundDescription']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
