<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: accountLogin.php");
    exit();
}

$username = $_SESSION['username'];

// Query to get distinct courses and batch sessions for filter options
$courseQuery = "SELECT DISTINCT Course FROM student";
$courseResult = $conn->query($courseQuery);
$selectedCourse = '';

$batchQuery = "SELECT DISTINCT batchSession FROM student";
$batchResult = $conn->query($batchQuery);
$selectedBatch = '';

$selectedRefundStatus = '';
$selectedStartDate = '';
$selectedEndDate = '';
$selectedDepartmentDues = '';

$conditions = [];
$params = [];

// Handle form submission
if (isset($_POST['filter'])) {
    if (!empty($_POST['course'])) {
        $selectedCourse = $_POST['course'];
        $conditions[] = "s.Course = ?";
        $params[] = $selectedCourse;
    }

    if (!empty($_POST['batchSession'])) {
        $selectedBatch = $_POST['batchSession'];
        $conditions[] = "s.batchSession = ?";
        $params[] = $selectedBatch;
    }

    if (!empty($_POST['refundStatus'])) {
        $selectedRefundStatus = $_POST['refundStatus'];
        $conditions[] = "rr.refundStatus = ?";
        $params[] = $selectedRefundStatus;
    }

    if (!empty($_POST['startDate'])) {
        $selectedStartDate = $_POST['startDate'];
        $conditions[] = "rr.requestDate >= ?";
        $params[] = $selectedStartDate;
    }

    if (!empty($_POST['endDate'])) {
        $selectedEndDate = $_POST['endDate'];
        $conditions[] = "rr.requestDate <= ?";
        $params[] = $selectedEndDate;
    }

    if (!empty($_POST['departmentDues'])) {
        $selectedDepartmentDues = $_POST['departmentDues'];
        if ($selectedDepartmentDues == 'Cleared') {
            $conditions[] = "(SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId AND n.noDueApproval = 'Yes') = 
            (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId)";
        } elseif ($selectedDepartmentDues == 'Not Cleared') {
            $conditions[] = "(SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId AND n.noDueApproval = 'Yes') < 
            (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId)";
        }
    }
}

$query = "SELECT s.rollNo, s.name, s.course, s.batchSession, rr.requestId, s.securityAmount, rr.requestDate, rr.refundStatus, rr.refundDate, rr.refundDescription,
          (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId AND n.noDueApproval = 'Yes') as countYes,
          (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId) as totalDepts,
          uc.filePath, uc.accHolderName, uc.bankName, uc.accountNo, uc.ifscCode
          FROM refundrequest rr
          JOIN student s ON rr.rollNo = s.rollNo
          LEFT JOIN uploadcheque uc ON s.rollNo = uc.rollNo";


if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY s.rollNo";

// Prepare and execute the query
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $paramTypes = str_repeat("s", count($params)); // Assuming all parameters are strings
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Refund Requests</title>
    <link rel="stylesheet" href="../css/noduesproject.css">
    <link rel="stylesheet" href="../css/accountDashboard.css">
</head>

<body>
    <div class="container">
        <header>
            <div class="header-item">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="welcome-message">
                        <h2>No Dues Refund Initiation</h2>
                    </div>
                <?php endif; ?>
            </div>
            <?php include 'accountNav.php'; ?>
        </header>

        <div id="filterSection">
            <form method="POST" action="">
                <div class="filter-group">
                    <label for="course">Filter by Course:</label>
                    <select name="course" id="course">
                        <option value="">Select Course</option>
                        <?php while ($courseRow = $courseResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($courseRow['Course']); ?>"
                                <?php if ($selectedCourse == $courseRow['Course']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($courseRow['Course']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="batchSession">Filter by Batch:</label>
                    <select name="batchSession" id="batchSession">
                        <option value="">Select Batch</option>
                        <?php while ($batchRow = $batchResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($batchRow['batchSession']); ?>"
                                <?php if ($selectedBatch == $batchRow['batchSession']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($batchRow['batchSession']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="refundStatus">Filter by Refund Initiated:</label>
                    <select name="refundStatus" id="refundStatus">
                        <option value="">Select Status</option>
                        <option value="Yes" <?php if ($selectedRefundStatus == 'Yes') echo 'selected'; ?>>Initiated</option>
                        <option value="No" <?php if ($selectedRefundStatus == 'No') echo 'selected'; ?>>Non Initiated</option>
                    </select>
                </div>

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
                        <option value="Cleared" <?php if ($selectedDepartmentDues == 'Cleared') echo 'selected'; ?>>Dues Cleared</option>
                        <option value="Not Cleared" <?php if ($selectedDepartmentDues == 'Not Cleared') echo 'selected'; ?>>Dues Not Cleared</option>
                    </select>
                </div>

                <button type="submit" id="filter" name="filter">Filter</button>

                <button type="submit" style="margin: 3px auto;" name="generate_report" formaction="generateReport.php" formtarget="_blank">Generate Report</button>

                
            </form>
        </div>

        <h3>Details of No Dues requested by Students</h3>
        <div class="table-container">
            <?php if ($result->num_rows > 0): ?>
                <table id="refundTable">
                    <thead>
                        <tr>
                            <th>Roll Number</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Security Amount</th>
                            <th>Request Date</th>
                            <th>Account Details</th>
                            <th>Cheque</th>
                            <th>No Dues Status</th>
                            <th>Refund Date</th>
                            <th>Refund Description</th>
                            <th>Refund Initiation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['rollNo']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['course']); ?></td>
                                <td><?php echo htmlspecialchars($row['securityAmount']); ?></td>
                                <td><?php echo htmlspecialchars($row['requestDate']); ?></td>
                                <td>
                                    <strong>A/c Holder Name:</strong> <?php echo htmlspecialchars($row['accHolderName']); ?><br>
                                    <strong>Bank Name:</strong> <?php echo htmlspecialchars($row['bankName']); ?><br>
                                    <strong>Account No:</strong> <?php echo htmlspecialchars($row['accountNo']); ?><br>
                                    <strong>IFSC Code:</strong> <?php echo htmlspecialchars($row['ifscCode']); ?><br>
                                </td>
                                <td><a href="<?php echo htmlspecialchars($row['filePath']); ?>" target="_blank">View Uploaded Cheque</a></td>
                                <td>
                                    <?php echo $row['countYes'] == $row['totalDepts'] ? 'Cleared' : 'Not Cleared'; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['refundDate']); ?></td>
                                <td><?php echo htmlspecialchars($row['refundDescription']); ?></td>
                                <td>
                                    <?php if ($row['refundStatus'] == 'Yes'): ?>
                                        Refund is initiated
                                    <?php else: ?>
                                        Refund is not initiated
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p id="noRecord">No records available for the selected filters.</p>
            <?php endif; ?>
        </div>
    </div>


</body>

</html>