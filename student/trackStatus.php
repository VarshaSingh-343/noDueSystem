<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['rollno'])) {
    header("Location: studentLogin.php");
    exit();
}

$rollNo = $_SESSION['rollno'];

$query = "SELECT requestId, refundStatus, refundDate, refundDescription FROM refundrequest WHERE rollNo = ?";
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
} else {
    $_SESSION['error_message'] = "No request found for this roll number.";
    header("Location: noDuesRequest.php");
    exit();
}

// Retrieve the no dues status for each department with deptName from the nodues table
$query = "SELECT d.deptName, n.noDueApproval, n.noDueComment, n.approvalDate 
          FROM nodues n 
          JOIN department d ON n.deptId = d.deptId 
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
    <link rel="stylesheet" href="studentDashboard.css">
    <style>
        h2{
            text-align: center;
        }
        table {
            width: 80%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #4A148C;
            color: white;
        }

        .refund-info {
            margin-top : 10px;
            background-color: #f9f9f9;
            padding: 5px 20px 10px 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 60%;
        }

        .refund-info p {
            margin: 10px 0;
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
                                <td><?php echo htmlspecialchars($row['noDueApproval']); ?></td>
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
                    <p><strong>Refund Status:</strong> <?php echo htmlspecialchars($refundStatus); ?></p>
                    <p><strong>Refund Date:</strong> <?php echo htmlspecialchars($refundDate); ?></p>
                    <p><strong>Refund Description:</strong> <?php echo htmlspecialchars($refundDescription); ?></p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
