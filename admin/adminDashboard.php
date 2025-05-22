<?php
session_start();
include '../connect.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: adminLogin.php");
    exit();
}

$countStudent = "SELECT COUNT(*) AS studentCount FROM student";
$resultStudent = $conn->query($countStudent);
$studentCount = $resultStudent->fetch_assoc()['studentCount'];

$countRequest = "SELECT COUNT(*) AS refundRequestCount FROM refundrequest";
$resultRequest = $conn->query($countRequest);
$refundRequestCount = $resultRequest->fetch_assoc()['refundRequestCount'];

$yesRequest = "SELECT COUNT(*) AS yesCount FROM refundrequest where refundStatus = 'Yes'";
$resultYes = $conn->query($yesRequest);
$yesCount = $resultYes->fetch_assoc()['yesCount'];

$noRequest = "SELECT COUNT(*) AS noCount FROM refundrequest where refundStatus = 'No'";
$resultNo = $conn->query($noRequest);
$noCount = $resultNo->fetch_assoc()['noCount'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/adminDashboard.css">
    <link rel="stylesheet" href="../css/noduesproject.css">
    
    <script>
        function navigateToPage(page) {
            window.location.href = page;
        }
    </script>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-item">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="welcome-message">
                        <h2>Hello <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                    </div>
                <?php endif; ?>
            </div>
            <?php include 'adminNav.php'; ?>
        </header>

        <div class="box">
            <div class="box-item" onclick="navigateToPage('viewStudent.php')"> 
                <div class="box-h3">
                    Number of students to apply for security money
                </div> 
                <div class="box-p">
                    <?php echo htmlspecialchars($studentCount); ?>
                </div>
            </div>

            <div class="box-item" onclick="navigateToPage('refundRequests.php')">
                <div class="box-h3" id="request">
                    Number of students who made request for no dues
                </div> 
                <div class="box-p">
                    <?php echo htmlspecialchars($refundRequestCount); ?>
                </div>
            </div>

            <div class="box-item" onclick="navigateToPage('notApprovedRequests.php')">
                <div class="box-h3" id="notApproved">
                    Number of students whose request for no dues is not approved
                </div> 
                <div class="box-p">
                    <?php echo htmlspecialchars($noCount); ?>
                </div>
            </div>
            
            <div class="box-item" onclick="navigateToPage('approvedRequests.php')"> 
                <div class="box-h3">
                    Number of students whose request for no dues is approved and refund initiated
                </div> 
                <div class="box-p">
                    <?php echo htmlspecialchars($yesCount); ?>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['alert']) && $_GET['alert'] === 'true'): ?>
            <script>
                alert('<?php echo isset($_SESSION['message']) ? addslashes($_SESSION['message']) : ''; ?>');
                <?php unset($_SESSION['message']); ?>
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
