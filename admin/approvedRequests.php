<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: adminLogin.php");
    exit();
}

$query = "SELECT * FROM refundrequest where refundStatus = 'Yes'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Requests</title>
    <link rel="stylesheet" href="../css/adminDashboard.css">
    <link rel="stylesheet" href="../css/noduesproject.css">
    <style>

    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-item">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="welcome-message">
                        <h2>Welcome to your Dashboard</h2>
                    </div>
                <?php endif; ?>
            </div>
            <?php include 'adminNav.php'; ?>
        </header>

        <div id="requestsSection">
            <h3>Details of Student whose security money is approved</h3>
            <table>
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th>Refund Date</th>
                        <!-- <th>Refund Status</th> -->
                        <th>Refund Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['rollNo']); ?></td>
                            <td><?php echo htmlspecialchars($row['refundDate']); ?></td>
                            <!-- <td><?php echo htmlspecialchars($row['refundStatus']); ?></td> -->
                            <td><?php echo htmlspecialchars($row['refundDescription']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
