<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: accountLogin.php");
    exit();
}

$username = $_SESSION['username'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Dashboard</title>
    <link rel="stylesheet" href="../css/noduesproject.css">
    <link rel="stylesheet" href="../css/accountDashboard.css">
    <style>
        
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-item">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="welcome-message">
                        <h2>Your Dashboard</h2>
                    </div>
                <?php endif; ?>
            </div>
            <?php include 'accountNav.php'; ?>
        </header>
        <main>
            <!-- <?php if (isset($_SESSION['success_message'])): ?>
                <div class="message"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error-message"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?> -->

            <div class = "button">
                <button class="toggle-btn" onclick="window.location.href='viewRefundRequests.php'">Students No Dues Refund Requests </button>
                <button class="toggle-btn" onclick="window.location.href='viewInitiatedRefunds.php'">View Students Initiated Refunds </button>
            </div>
        </main>

</body>
</html>
