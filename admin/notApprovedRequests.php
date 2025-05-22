<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: adminLogin.php");
    exit();
}

$query = "SELECT refundrequest.rollNo, nodues.noDueComment, department.deptName 
          FROM nodues 
          JOIN department ON nodues.deptId = department.deptId 
          JOIN refundrequest ON nodues.requestId = refundrequest.requestId 
          WHERE nodues.noDueApproval = 'No'";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Approved Refund Requests</title>
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

        <div id="notApprovedSection">
            <h3>Requests for No Dues but Not Approved</h3>
            <table>
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th>Department Name</th>
                        <th>No Due Comment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['rollNo']); ?></td>
                            <td><?php echo htmlspecialchars($row['deptName']); ?></td>
                            <td><?php echo htmlspecialchars($row['noDueComment']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
