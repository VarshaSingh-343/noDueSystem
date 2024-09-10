<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['deptId'])) {
    header("Location: departmentLogin.php");
    exit();
}

$deptId = $_SESSION['deptId'];

$deptNames = [];
$sql = "SELECT deptId, deptName FROM department";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $deptNames[$row['deptId']] = $row['deptName'];
    }
} else {
    die("No departments found.");
}

$departmentName = isset($deptNames[$deptId]) ? $deptNames[$deptId] : 'Unknown Department';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($departmentName); ?> Dashboard</title>
    <link rel="stylesheet" href="departmentDashboard.css">
    <style>
        .message {
            width: 40%;
            text-align: center;
            padding: 10px;
            margin: 0 auto;
            border-radius: 5px;
            border: 2px solid #f03e41;
        }
        .success {
            color: #08a820;
        }
        .error {
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-item">
                <?php if (isset($_SESSION['deptId'])): ?>
                    <div class="welcome-message">
                        <h2><?php echo htmlspecialchars($departmentName); ?> Dashboard</h2>
                    </div>
                <?php endif; ?>
            </div>
            <?php include 'deptNav.php'; ?>
        </header>

        <main>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="message"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error-message"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <a href="viewRefundRequests.php">
                <button class="toggle-btn">View Refund Requests</button>
            </a>


        </main>
    </div>

</body>
</html>
