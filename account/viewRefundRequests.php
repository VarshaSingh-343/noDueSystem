<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: accountLogin.php");
    exit();
}

$username = $_SESSION['username'];

// Initialize filter variables
$courseFilter = isset($_POST['course']) ? $_POST['course'] : '';
$batchFilter = isset($_POST['batchSession']) ? $_POST['batchSession'] : '';
$refundStatusFilter = isset($_POST['refundStatus']) ? $_POST['refundStatus'] : '';
$departmentDuesFilter = isset($_POST['departmentDues']) ? $_POST['departmentDues'] : '';

// Initialize date filters
$selectedStartDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
$selectedEndDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';

// Base query
$query = "SELECT s.rollNo, s.name, s.course, s.batchSession, rr.requestId, s.securityAmount, rr.requestDate,
          (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId AND n.noDueApproval = 'Yes') as countYes,
          (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId) as totalDepts,
          uc.filePath
          FROM refundrequest rr
          JOIN student s ON rr.rollNo = s.rollNo
          LEFT JOIN uploadcheque uc ON s.rollNo = uc.rollNo
          WHERE rr.refundStatus != 'Yes'";

// Add filters to the query
$conditions = [];
$bindParams = [];
$bindTypes = '';

if ($courseFilter) {
    $conditions[] = "s.course = ?";
    $bindParams[] = $courseFilter;
    $bindTypes .= 's';
}
if ($batchFilter) {
    $conditions[] = "s.batchSession = ?";
    $bindParams[] = $batchFilter;
    $bindTypes .= 's';
}
if ($refundStatusFilter) {
    if ($refundStatusFilter == 'Yes') {
        $conditions[] = "rr.refundStatus = 'Yes'";
    } elseif ($refundStatusFilter == 'No') {
        $conditions[] = "rr.refundStatus = 'No'";
    }
}

if ($selectedStartDate) {
    $conditions[] = "rr.requestDate >= ?";
    $bindParams[] = $selectedStartDate;
    $bindTypes .= 's';
}
if ($selectedEndDate) {
    $conditions[] = "rr.requestDate <= ?";
    $bindParams[] = $selectedEndDate;
    $bindTypes .= 's';
}
if ($departmentDuesFilter) {
    if ($departmentDuesFilter == 'Cleared') {
        $conditions[] = "(SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId AND n.noDueApproval = 'Yes') = 
                        (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId)";
    } elseif ($departmentDuesFilter == 'Not Cleared') {
        $conditions[] = "(SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId AND n.noDueApproval = 'Yes') < 
                        (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId)";
    }
}

if (count($conditions) > 0) {
    $query .= " AND " . implode(' AND ', $conditions);
}

// Prepare and execute the statement
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

// Bind parameters
if (!empty($bindParams)) {
    $stmt->bind_param($bindTypes, ...$bindParams);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Refund Requests</title>
    <link rel="stylesheet" href="accountDashboard.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-item">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="welcome-message">
                        <h2>Refund Requests</h2>
                    </div>
                <?php endif; ?>
            </div>
            <?php include 'accountNav.php'; ?>
        </header>

        <!-- HTML Code - Filter Form -->
<div id="filterSection">
    <form method="POST" action="">
        <label for="course">Filter by Course:</label>
        <select name="course" id="course">
            <option value="">Select Course</option>
            <?php
            $courseQuery = "SELECT DISTINCT course FROM student";
            $courseResult = $conn->query($courseQuery);
            while ($courseRow = $courseResult->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($courseRow['course']); ?>"
                    <?php if ($courseFilter == $courseRow['course']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($courseRow['course']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="batchSession">Filter by Batch:</label>
        <select name="batchSession" id="batchSession">
            <option value="">Select Batch</option>
            <?php
            $batchQuery = "SELECT DISTINCT batchSession FROM student";
            $batchResult = $conn->query($batchQuery);
            while ($batchRow = $batchResult->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($batchRow['batchSession']); ?>"
                    <?php if ($batchFilter == $batchRow['batchSession']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($batchRow['batchSession']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="refundStatus">Filter by Refund Status:</label>
                <select name="refundStatus" id="refundStatus">
                    <option value="">Select Status</option>
                    <option value="Yes" <?php if ($refundStatusFilter == 'Yes') echo 'selected'; ?>>Refunded</option>
                    <option value="No" <?php if ($refundStatusFilter == 'No') echo 'selected'; ?>>Non Refunded</option>
                </select>

        <div class="filter-group">
            <label for="startDate">Start Date:</label>
            <input type="date" name="startDate" id="startDate" value="<?php echo htmlspecialchars($selectedStartDate); ?>">
        </div>

        <div class="filter-group">
            <label for="endDate">End Date:</label>
            <input type="date" name="endDate" id="endDate" value="<?php echo htmlspecialchars($selectedEndDate); ?>">
        </div>

        <div class="filter-group">
            <label for="departmentDues">Filter by Department Dues:</label>
            <select name="departmentDues" id="departmentDues">
                <option value="">Select Dues Status</option>
                <option value="Cleared" <?php if ($departmentDuesFilter == 'Cleared') echo 'selected'; ?>>Dues Cleared</option>
                <option value="Not Cleared" <?php if ($departmentDuesFilter == 'Not Cleared') echo 'selected'; ?>>Dues Not Cleared</option>
            </select>
        </div>

        <button type="submit" id="filter" name="filter">Filter</button>
    </form>
</div>

            
        <h3>Details of No Dues of Students</h3>
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
                            <?php echo $row['countYes'] == $row['totalDepts'] ? 'Cleared' : 'Not Cleared'; ?> 
                        </td>
                        <td>
                            <?php if ($refundStatusFilter == 'Yes'): ?>
                                <?php if ($row['refundStatus'] == 'Yes'): ?>
                                    <?php echo "Refund Initiated"; ?>
                                <?php endif; ?>
                            <?php elseif ($refundStatusFilter == 'No'): ?>
                                <?php if ($row['countYes'] == $row['totalDepts']): ?>
                                    <button id="greenYes" onclick="openModal('<?php echo $row['requestId']; ?>', '<?php echo $row['filePath']; ?>')">Initiate Refund</button>
                                <?php else: ?>
                                    <button id="redNo">No Refund</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>

                </tbody>
            </table>
        </div>
    
    </div>

    <div id="refundModal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Initiate Refund</h2>
        <form id="refundForm" method="POST" action="processRefund.php">
            <input type="hidden" name="requestId" id="requestId">
            <div class="form-group">
                <label for="filePath">Uploaded Check:</label>
                <a id="checkImage" href="#" target="_blank">View Uploaded Cheque</a>
            </div>
            <div class="form-group">
                <label for="refundDescription">Refund Description:</label>
                <textarea name="refundDescription" id="refundDescription" required></textarea>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>
</div>

<script>
    function openModal(requestId, filePath) {
        document.getElementById('requestId').value = requestId;
        // Update the href of the link with the filePath
        var checkImageLink = document.getElementById('checkImage');
        checkImageLink.href = filePath;
        checkImageLink.innerText = "View Uploaded Cheque"; // Update link text if needed
        document.getElementById('refundModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('refundModal').style.display = 'none';
    }
</script>

</body>
</html>
