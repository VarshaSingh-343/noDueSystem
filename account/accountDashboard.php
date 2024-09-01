<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: accountLogin.php");
    exit();
}

$username = $_SESSION['username'];

$query = "SELECT s.rollNo, s.name, s.course, s.batchSession, rr.requestId, s.securityAmount, rr.requestDate,
          (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId AND n.noDueApproval = 'Yes') as countYes,
          (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId) as totalDepts,
          uc.filePath
          FROM refundrequest rr
          JOIN student s ON rr.rollNo = s.rollNo
          LEFT JOIN uploadcheque uc ON s.rollNo = uc.rollNo
          WHERE rr.refundStatus != 'Yes'";

$stmt = $conn->prepare($query);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}

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
    <title>Account Dashboard</title>
    <link rel="stylesheet" href="accountDashboard.css">
    <style>
        #refundTable, #initiatedRefundTable {
            display: none;
            width: 100%;
            margin-top: 20px;
        }
        
        .toggle-btn {
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-item">
                <h2>Account Dashboard</h2>
            </div>
            <nav>
                <ul>
                    <li><a class="nav-link" href="accountDashboard.php">Dashboard</a></li>
                    <li>|</li>
                    <li><a class="nav-link" href="accountLogout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="message"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error-message"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <div>
                <button class="toggle-btn" onclick="showTable('refundTable')">View Refund Requests</button>
                <button class="toggle-btn" onclick="showTable('initiatedRefundTable')">View Initiated Refunds</button>
            </div>

            <div class="table-container">
                <table id="refundTable">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Roll Number</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Security Amount</th>
                            <th>Request Date</th>
                            <th>No Dues Status</th> 
                            <th>Refund Initiation</th>        
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['requestId']); ?></td>
                                <td><?php echo htmlspecialchars($row['rollNo']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['course']); ?></td>
                                <td><?php echo htmlspecialchars($row['batchSession']); ?></td>
                                <td><?php echo htmlspecialchars($row['securityAmount']); ?></td>
                                <td><?php echo htmlspecialchars($row['requestDate']); ?></td>
                                <td>
                                    <?php echo $row['countYes'] == $row['totalDepts'] ? 'Yes' : 'No'; ?> 
                                </td>
                                <td>
                                    <?php if ($row['countYes'] == $row['totalDepts']): ?>
                                        <button id="greenYes" onclick="openModal('<?php echo $row['requestId']; ?>', '<?php echo $row['filePath']; ?>')">Initiate Refund</button>
                                    <?php else: ?>
                                        <button id="redNo">No Refund</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

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

    <div id="refundModal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Initiate Refund</h2>
            <form id="refundForm" method="POST" action="processRefund.php">
                <input type="hidden" name="requestId" id="requestId">
                <div class="form-group">
                    <label for="filePath">Uploaded Cheque:</label>
                    <a id="fileLink" href="" target="_blank">View File</a>
                </div>
                <div class="form-group">
                    <label for="refundDescription">Refund Description:</label>
                    <textarea id="refundDescription" name="refundDescription" required></textarea>
                </div>
                <button type="submit" id="submit">Submit</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(requestId, filePath) {
            document.getElementById('refundModal').style.display = 'block';
            document.getElementById('requestId').value = requestId;
            document.getElementById('fileLink').href = filePath;
        }

        function closeModal() {
            document.getElementById('refundModal').style.display = 'none';
        }

        function showTable(tableId) {
            document.getElementById('refundTable').style.display = 'none';
            document.getElementById('initiatedRefundTable').style.display = 'none';
            document.getElementById(tableId).style.display = 'table';
        }
    </script>
</body>
</html>
