<?php

session_start();
include '../connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: adminLogin.php");
    exit();
}

// Fetch distinct values for course and batch
$courseQuery = "SELECT DISTINCT Course FROM student";
$courseResult = $conn->query($courseQuery);

$batchQuery = "SELECT DISTINCT batchSession FROM student";
$batchResult = $conn->query($batchQuery);

// Initialize query parts
$studentConditions = [];
$studentParams = [];
$studentParamTypes = '';

$refundConditions = [];
$refundParams = [];
$refundParamTypes = '';

$showStudentTable = true;
$showRefundTable = false;
$studentResult = null;

if (isset($_POST['filter'])) {
    if (!empty($_POST['course'])) {
        $selectedCourse = $_POST['course'];
        $studentConditions[] = "Course = ?";
        $studentParams[] = $selectedCourse;
        $studentParamTypes .= 's';
        $showRefundTable = false;
    }

    if (!empty($_POST['batchSession'])) {
        $selectedBatch = $_POST['batchSession'];
        $studentConditions[] = "batchSession = ?";
        $studentParams[] = $selectedBatch;
        $studentParamTypes .= 's';
        $showRefundTable = false;
    }

    if (!empty($_POST['refundRequest']) || !empty($_POST['refundStatus'])) {
        $showRefundTable = true;
        $showStudentTable = false;

        if (!empty($_POST['refundRequest'])) {
            $refundRequest = $_POST['refundRequest'];
            if ($refundRequest == 'Requested') {
                $refundConditions[] = "refundrequest.rollNo IS NOT NULL";
            } elseif ($refundRequest == 'Non Requested') {
                $refundConditions[] = "refundrequest.rollNo IS NULL";
            }
        }

        if (!empty($_POST['refundStatus'])) {
            $refundStatus = $_POST['refundStatus'];
            if ($refundStatus == 'Refunded') {
                $refundConditions[] = "refundrequest.refundStatus = 'Yes'";
            } elseif ($refundStatus == 'Non Refunded') {
                $refundConditions[] = "refundrequest.refundStatus = 'No'";
            }
        }
    }

    if ($showRefundTable) {
        // Build the refund query
        $refundQuery = "
            SELECT student.rollNo, student.Name, student.Course, 
                   refundrequest.requestId, refundrequest.requestDate, 
                   refundrequest.refundDate, refundrequest.refundDescription, 
                   refundrequest.refundStatus
            FROM student
            LEFT JOIN refundrequest ON student.rollNo = refundrequest.rollNo
            WHERE 1=1";

        if (!empty($studentConditions)) {
            $refundQuery .= " AND " . implode(" AND ", $studentConditions);
        }

        if (!empty($refundConditions)) {
            $refundQuery .= " AND " . implode(" AND ", $refundConditions);
        }

        $refundStmt = $conn->prepare($refundQuery);
        if ($refundStmt) {
            if (!empty($studentParams)) {
                $refundStmt->bind_param($studentParamTypes, ...$studentParams);
            }
            $refundStmt->execute();
            $refundResult = $refundStmt->get_result();
        } else {
            die("Failed to prepare refund query: " . $conn->error);
        }
    }
}

$studentQuery = "SELECT * FROM student";
if (!empty($studentConditions)) {
    $studentQuery .= " WHERE " . implode(" AND ", $studentConditions);
}

if (!$showRefundTable) {
    $studentStmt = $conn->prepare($studentQuery);
    if ($studentStmt) {
        if (!empty($studentParams)) {
            $studentStmt->bind_param($studentParamTypes, ...$studentParams);
        }
        $studentStmt->execute();
        $studentResult = $studentStmt->get_result();
    } else {
        die("Failed to prepare student query: " . $conn->error);
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Data</title>
    <link rel="stylesheet" href="../css/adminDashboard.css">
    <link rel="stylesheet" href="../css/noduesproject.css">
    <style>
        p {
            text-align: center;
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

        <div class="importButton">
            <button id="openModalBtn">Import Students Data</button>
        </div>

        <div id="filterSection">
            <form method="POST" action="">
                <div class="filter-group">
                    <label for="course">Filter Course:</label>
                    <select name="course" id="course">
                        <option value="">Select Course</option>
                        <?php while ($courseRow = $courseResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($courseRow['Course']); ?>"
                                <?php if (isset($_POST['course']) && $_POST['course'] == $courseRow['Course']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($courseRow['Course']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="batchSession">Filter Batch:</label>
                    <select name="batchSession" id="batchSession">
                        <option value="">Select Batch</option>
                        <?php while ($batchRow = $batchResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($batchRow['batchSession']); ?>"
                                <?php if (isset($_POST['batchSession']) && $_POST['batchSession'] == $batchRow['batchSession']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($batchRow['batchSession']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="refundRequest">Refund Request:</label>
                    <select name="refundRequest" id="refundRequest">
                        <option value="">Select Option</option>
                        <option value="Requested" <?php if (isset($_POST['refundRequest']) && $_POST['refundRequest'] == 'Requested') echo 'selected'; ?>>Requested</option>
                        <option value="Non Requested" <?php if (isset($_POST['refundRequest']) && $_POST['refundRequest'] == 'Non Requested') echo 'selected'; ?>>Non Requested</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="refundStatus">Refund Status:</label>
                    <select name="refundStatus" id="refundStatus">
                        <option value="">Select Status</option>
                        <option value="Refunded" <?php if (isset($_POST['refundStatus']) && $_POST['refundStatus'] == 'Refunded') echo 'selected'; ?>>Refunded</option>
                        <option value="Non Refunded" <?php if (isset($_POST['refundStatus']) && $_POST['refundStatus'] == 'Non Refunded') echo 'selected'; ?>>Non Refunded</option>
                    </select>
                </div>

                <button type="submit" id="filter" name="filter">Filter</button>
            </form>
        </div>

        <?php include 'importData.php'; ?>

        <?php if (isset($_GET['alert']) && $_GET['alert'] === 'true'): ?>
            <script>
                alert('<?php echo isset($_SESSION['message']) ? addslashes($_SESSION['message']) : ''; ?>');
                <?php unset($_SESSION['message']); ?>
            </script>
        <?php endif; ?>

        <?php if ($showRefundTable && $refundResult->num_rows > 0): ?>
            <h2>Refund Details</h2>
            <table>
                <tr>
                    <th>Roll No</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Request ID</th>
                    <th>Request Date</th>
                    <th>Refund Date</th>
                    <th>Refund Description</th>
                    <th>Refund Status</th>
                </tr>
                <?php while ($row = $refundResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['rollNo']); ?></td>
                        <td><?php echo htmlspecialchars($row['Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['Course']); ?></td>
                        <td><?php echo htmlspecialchars($row['requestId']); ?></td>
                        <td><?php echo htmlspecialchars($row['requestDate']); ?></td>
                        <td><?php echo htmlspecialchars($row['refundDate']); ?></td>
                        <td><?php echo htmlspecialchars($row['refundDescription']); ?></td>
                        <td><?php echo htmlspecialchars($row['refundStatus']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php elseif ($showStudentTable && $studentResult->num_rows > 0): ?>
            <h2>Student Details</h2>
            <table>
                <tr>
                    <th>Batch Session</th>
                    <th>Enrollment No</th>
                    <th>Roll No</th>
                    <th>Course</th>
                    <th>Name</th>
                    <th>Father's Name</th>
                    <th>Mother's Name</th>
                    <th>Contact No</th>
                    <th>Date Of Birth</th>
                    <th>Security Amount</th>
                </tr>
                <?php while ($row = $studentResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['batchSession']); ?></td>
                        <td><?php echo htmlspecialchars($row['enrollmentNo']); ?></td>
                        <td><?php echo htmlspecialchars($row['rollNo']); ?></td>
                        <td><?php echo htmlspecialchars($row['Course']); ?></td>
                        <td><?php echo htmlspecialchars($row['Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['fatherName']); ?></td>
                        <td><?php echo htmlspecialchars($row['motherName']); ?></td>
                        <td><?php echo htmlspecialchars($row['Contact']); ?></td>
                        <td><?php echo htmlspecialchars($row['Dob']); ?></td>
                        <td><?php echo htmlspecialchars($row['securityAmount']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No data found for the selected filters.</p>
        <?php endif; ?>

    </div>
</body>

</html>