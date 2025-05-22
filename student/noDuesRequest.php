<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['rollno'])) {
    header("Location: studentLogin.php");
    exit();
}

$rollNo = $_SESSION['rollno'];
$requestSubmitted = false;
$refundStatus = '';
$uploadedCheque = '';
$buttonText = "Upload Details";  // Default button text

// Check if the student has already submitted a request
$query = "SELECT * FROM refundrequest WHERE rollNo = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $rollNo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $requestSubmitted = true;
    $row = $result->fetch_assoc();
    $refundStatus = $row['refundStatus'];
    $verifyDetails = $row['verifyDetails']; 
}

// Check if the student has uploaded a cheque
$chequeQuery = "SELECT * FROM uploadcheque WHERE rollNo = ?";
$chequeStmt = $conn->prepare($chequeQuery);
$chequeStmt->bind_param("s", $rollNo);
$chequeStmt->execute();
$chequeResult = $chequeStmt->get_result();

$chequeData = $chequeResult->fetch_assoc();

if ($chequeData) {
    $uploadedCheque = $chequeData['filePath'];
    $buttonText = "Update Details";  // Change button text if a cheque is already uploaded
    $accHolderName = $chequeData['accHolderName'];
    $bankName = $chequeData['bankName'];
    $accountNo = $chequeData['accountNo'];
    $ifscCode = $chequeData['ifscCode'];
} else {
    $accHolderName = '';
    $bankName = '';
    $accountNo = '';
    $ifscCode = '';
}

$chequeStmt->close();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No Dues Request</title>
    <link rel="stylesheet" href="../css/noDuesRequest.css">
    <style>
        .faded {
            opacity: 0.3;
            pointer-events: none;
        }

        .disabled-field {
            background-color: #f0f0f0;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="header-item">
                <?php if (isset($_SESSION['rollno'])): ?>
                    <div class="welcome-message">
                        Request No Dues!
                    </div>
                <?php endif; ?>
            </div>
            <?php include 'nav.php'; ?>
        </header>

        <main>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="message"><?php echo $_SESSION['success_message'];
                                        unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="message error"><?php echo $_SESSION['error_message'];
                                            unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <?php if ($requestSubmitted): ?>
                <div class="message">You have already submitted a No Dues Request.</div>
            <?php endif; ?>

            <?php if ($requestSubmitted): ?>
                <form action="trackStatus.php" method="get">
                    <button type="submit" id="track">Track Refund Status</button>
                </form>
            <?php endif; ?>

            <?php if ($verifyDetails !== 'Verified'): ?>
                <form action="processRequest.php" method="post" enctype="multipart/form-data" id="detailsForm">
                    <h2>Upload/Update your Account Details</h2>
                    <div class="form-item">
                        <label for="accHolderName">Account Holder Name:</label>
                        <input type="text" name="accHolderName" id="accHolderName" value="<?php echo htmlspecialchars($accHolderName); ?>">
                    </div>

                    <div class="form-item">
                        <label for="bankName">Bank Name:</label>
                        <input type="text" name="bankName" id="bankName" value="<?php echo htmlspecialchars($bankName); ?>">
                    </div>

                    <div class="form-item">
                        <label for="accountNo">Account Number:</label>
                        <input type="text" name="accountNo" id="accountNo" value="<?php echo htmlspecialchars($accountNo); ?>" required pattern="\d{5,20}" title="Account number must be numeric and between 5 and 18 digits.">
                    </div>

                    <div class="form-item">
                        <label for="ifscCode">IFSC Code:</label>
                        <input type="text" name="ifscCode" id="ifscCode" value="<?php echo htmlspecialchars($ifscCode); ?>">
                    </div>

                    <div class="form-item">
                        <label for="file">Upload your cancelled cheque:</label>
                        <input type="file" name="file" id="file" accept="application/pdf">
                    </div>

                    <div class="info">
                        *Allowed type: PDF<br>
                        *Max size: 2MB
                    </div>

                    <button id="updatecheque" type="submit"><?php echo $buttonText; ?></button>
                </form>



                <?php if ($uploadedCheque): ?>
                    <div class="uploaded-cheque">
                        <h3>Your Uploaded Cheque:</h3>
                        <a href="<?php echo htmlspecialchars($uploadedCheque); ?>" target="_blank">View Uploaded Cheque</a>
                    </div>
                <?php endif; ?>

            <?php else: ?>

                <div id="detailsForm">
                    <h2>Uploaded Account Details</h2>
                    <div class="form-item">
                        <label for="accHolderName">Account Holder Name:</label>
                        <input type="text" name="accHolderName" id="accHolderName" value="<?php echo htmlspecialchars($accHolderName); ?>" class="disabled-field" disabled>
                    </div>

                    <div class="form-item">
                        <label for="bankName">Bank Name:</label>
                        <input type="text" name="bankName" id="bankName" value="<?php echo htmlspecialchars($bankName); ?>" class="disabled-field" disabled>
                    </div>

                    <div class="form-item">
                        <label for="accountNo">Account Number:</label>
                        <input type="text" name="accountNo" id="accountNo" value="<?php echo htmlspecialchars($accountNo); ?>" class="disabled-field" disabled>
                    </div>

                    <div class="form-item">
                        <label for="ifscCode">IFSC Code:</label>
                        <input type="text" name="ifscCode" id="ifscCode" value="<?php echo htmlspecialchars($ifscCode); ?>" class="disabled-field" disabled>
                    </div>

                    <div class="uploaded-cheque">
                        <h3>Your Uploaded Cheque:</h3>
                        <a href="<?php echo htmlspecialchars($uploadedCheque); ?>" target="_blank">View Uploaded Cheque</a>
                    </div>
                </div>
                <div class="message">Your Refund has already been processed. You cannot change the uploaded details.</div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>