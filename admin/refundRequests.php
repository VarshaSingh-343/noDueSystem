<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: adminLogin.php");
    exit();
}

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
        $conditions[] = "student.Course = ?";
        $params[] = $selectedCourse;
    }

    if (!empty($_POST['batchSession'])) {
        $selectedBatch = $_POST['batchSession'];
        $conditions[] = "student.batchSession = ?";
        $params[] = $selectedBatch;
    }

    if (!empty($_POST['refundStatus'])) {
        $selectedRefundStatus = $_POST['refundStatus'];
        $conditions[] = "refundrequest.refundStatus = ?";
        $params[] = $selectedRefundStatus;
    }

    if (!empty($_POST['startDate'])) {
        $selectedStartDate = $_POST['startDate'];
        $conditions[] = "refundrequest.requestDate >= ?";
        $params[] = $selectedStartDate;
    }

    if (!empty($_POST['endDate'])) {
        $selectedEndDate = $_POST['endDate'];
        $conditions[] = "refundrequest.requestDate <= ?";
        $params[] = $selectedEndDate;
    }

    if (!empty($_POST['departmentDues'])) {
        $selectedDepartmentDues = $_POST['departmentDues'];
        if ($selectedDepartmentDues == 'Cleared') {
            $conditions[] = "NOT EXISTS (
                SELECT 1 FROM nodues n WHERE n.requestId = refundrequest.requestId AND n.noDueApproval = 'No'
            )";
        } elseif ($selectedDepartmentDues == 'Not Cleared') {
            $conditions[] = "EXISTS (
                SELECT 1 FROM nodues n WHERE n.requestId = refundrequest.requestId AND n.noDueApproval = 'No'
            )";
        }
    }
}

$query = "SELECT refundrequest.rollNo, student.Name, student.Course, refundrequest.requestDate, refundrequest.refundStatus, 
                 refundrequest.refundDate, refundrequest.refundDescription,
                 nodues.deptId, nodues.noDueApproval, nodues.noDueComment, department.deptName,
                 uploadcheque.filePath
          FROM refundrequest 
          JOIN student ON refundrequest.rollNo = student.rollNo
          LEFT JOIN nodues ON nodues.requestId = refundrequest.requestId
          LEFT JOIN department ON nodues.deptId = department.deptId
          LEFT JOIN uploadcheque ON uploadcheque.rollNo = student.rollNo";

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY refundrequest.rollNo, department.deptId";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $paramTypes = str_repeat("s", count($params));
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$refundData = [];
while ($row = $result->fetch_assoc()) {
    $rollNo = $row['rollNo'];
    if (!isset($refundData[$rollNo])) {
        $refundData[$rollNo] = [
            'Name' => $row['Name'],
            'rollNo' => $row['rollNo'],
            'Course' => $row['Course'],
            'requestDate' => $row['requestDate'],
            'refundStatus' => $row['refundStatus'],
            'refundDate' => $row['refundDate'],
            'refundDescription' => $row['refundDescription'],
            'departments' => [],
            'filePath' => $row['filePath']
        ];
    }
    $refundData[$rollNo]['departments'][$row['deptName']] = [
        'noDueApproval' => $row['noDueApproval'],
        'noDueComment' => $row['noDueComment']
    ];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View No Dues Details</title>
    <link rel="stylesheet" href="../css/adminDashboard.css">
    <link rel="stylesheet" href="../css/noduesproject.css">
    <style>
        table {
            width: 100%;
        }

        td {
            vertical-align: top;
        }

        .dept-status {
            font-weight: bold;
        }

        .dept-comment {
            margin-top: 5px;
            font-size: smaller;
            color: #555;
        }
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
                    <label for="refundStatus">Filter by Refund Status:</label>
                    <select name="refundStatus" id="refundStatus">
                        <option value="">Select Status</option>
                        <option value="Yes" <?php if ($selectedRefundStatus == 'Yes') echo 'selected'; ?>>Refunded</option>
                        <option value="No" <?php if ($selectedRefundStatus == 'No') echo 'selected'; ?>>Non Refunded</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="startDate">Request Date From:</label>
                    <input type="date" name="startDate" id="startDate" value="<?php echo htmlspecialchars($selectedStartDate); ?>">
                </div>

                <div class="filter-group">
                    <label for="endDate">To:</label>
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

                <button type="submit" style="margin: 3px auto;" name="generate_report" formaction="adminGenerateReport.php" formtarget="_blank">Generate Report</button>

            </form>
        </div>

        <div id="requestsSection">
            <h3>Details of Students Requesting for Security Money</h3>
            <?php if (!empty($refundData)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Request Date</th>
                            <?php
                            if ($departmentResult = $conn->query("SELECT deptName FROM department")) {
                                while ($deptRow = $departmentResult->fetch_assoc()) {
                                    echo "<th>{$deptRow['deptName']} Status & Comment</th>";
                                }
                            }
                            ?>
                            <th>Refund Status</th>
                            <th>Refund Date</th>
                            <th>Refund Description</th>
                            <th>Cheque</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($refundData as $rollNo => $data): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['rollNo']); ?></td>
                                <td><?php echo htmlspecialchars($data['Name']); ?></td>
                                <td><?php echo htmlspecialchars($data['Course']); ?></td>
                                <td><?php echo htmlspecialchars($data['requestDate']); ?></td>
                                <?php foreach ($data['departments'] as $deptName => $deptData): ?>
                                    <td>
                                        <div class="dept-status">
                                            <?php echo $deptData['noDueApproval'] == 'Yes' ? 'Cleared' : 'Not Cleared'; ?>
                                        </div>
                                        <div class="dept-comment">
                                            <?php echo empty($deptData['noDueComment']) || $deptData['noDueComment'] === 'NULL' ? 'No comments' : htmlspecialchars($deptData['noDueComment']); ?>
                                        </div>
                                    </td>
                                <?php endforeach; ?>
                                <td><?php echo htmlspecialchars($data['refundStatus'] == 'Yes' ? 'Refunded' : 'Non Refunded'); ?></td>
                                <td><?php echo htmlspecialchars($data['refundDate']); ?></td>
                                <td><?php echo htmlspecialchars($data['refundDescription']); ?></td>
                                <td>
                                    <?php if (!empty($data['filePath'])): ?>
                                        <a href="<?php echo htmlspecialchars($data['filePath']); ?>" class="cheque-link" target="_blank">View Cheque</a>
                                    <?php else: ?>
                                        Not Uploaded
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No student records found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>