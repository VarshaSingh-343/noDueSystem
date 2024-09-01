<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['rollno'])) {
    header("Location: studentLogin.php");
    exit();
}

$rollNo = $_SESSION['rollno'];
$requestSubmitted = false;

// Check if the student has already submitted a request
$query = "SELECT * FROM refundrequest WHERE rollNo = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $rollNo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $requestSubmitted = true;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No Dues Request</title>
    <link rel="stylesheet" href="noDuesRequest.css">
    <style>
        .faded {
            opacity: 0.5;
            pointer-events: none;
        }
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
        
        <main>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="message"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="message error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <?php if ($requestSubmitted): ?>
                <div class="message">You have already submitted a No Dues Request.</div>
                <form action="trackStatus.php" method="get">
                    <button type="submit">Track Refund Status</button>
                </form>
            <?php else: ?>
                <form action="processRequest.php" method="post" enctype="multipart/form-data">
                    <h2>Upload Cancelled Cheque</h2>
                    <label for="file">Select the canceled cheque to upload:</label>
                    <input type="file" name="file" id="file" accept="application/pdf" required>

                    <div class="info">
                        *Allowed type: PDF<br>
                        *Max size: 2MB
                    </div>

                    <button type="submit">Submit Request</button>
                </form>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
