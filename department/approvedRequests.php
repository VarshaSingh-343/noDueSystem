<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['deptId'])) {
    header("Location: departmentLogin.php");
    exit();
}

$deptId = $_SESSION['deptId'];

$deptNames = [];
$sql = "SELECT deptId, deptName FROM department";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching departments: " . $conn->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $deptNames[$row['deptId']] = $row['deptName'];
    }
} else {
    die("No departments found.");
}

$departmentName = isset($deptNames[$deptId]) ? $deptNames[$deptId] : 'Unknown Department';

$courseQuery = "SELECT DISTINCT Course FROM student";
$courseResult = $conn->query($courseQuery);

if (!$courseResult) {
    die("Error fetching courses: " . $conn->error);
}

$batchQuery = "SELECT DISTINCT batchSession FROM student";
$batchResult = $conn->query($batchQuery);

if (!$batchResult) {
    die("Error fetching batches: " . $conn->error);
}

$selectedCourse = '';
$selectedBatch = '';
$selectedStartDate = '';
$selectedEndDate = '';
$selectedDuesCleared = '';

$conditions = [];
$params = [];

$query = "SELECT s.rollNo, s.name, s.Course, nd.requestId, rr.requestDate, nd.noDueApproval, nd.noDueComment, nd.approvalDate
          FROM nodues nd
          JOIN refundrequest rr ON nd.requestId = rr.requestId
          JOIN student s ON rr.rollNo = s.rollNo
          WHERE nd.deptId = ?";
$conditions[] = $deptId;
$paramTypes = 's';

if (isset($_POST['filter'])) {
    if (!empty($_POST['course'])) {
        $selectedCourse = $_POST['course'];
        $conditions[] = $selectedCourse;
        $query .= " AND s.Course = ?";
        $paramTypes .= 's';
    }

    if (!empty($_POST['batchSession'])) {
        $selectedBatch = $_POST['batchSession'];
        $conditions[] = $selectedBatch;
        $query .= " AND s.batchSession = ?";
        $paramTypes .= 's';
    }

    if (!empty($_POST['startDate'])) {
        $selectedStartDate = $_POST['startDate'];
        $conditions[] = $selectedStartDate;
        $query .= " AND rr.requestDate >= ?";
        $paramTypes .= 's';
    }

    if (!empty($_POST['endDate'])) {
        $selectedEndDate = $_POST['endDate'];
        $conditions[] = $selectedEndDate;
        $query .= " AND rr.requestDate <= ?";
        $paramTypes .= 's';
    }

    if (!empty($_POST['duesCleared'])) {
        $selectedDuesCleared = $_POST['duesCleared'];
        if ($selectedDuesCleared == 'Cleared') {
            $query .= " AND (nd.requestId = rr.requestId AND nd.noDueApproval = 'Yes')";
        } elseif ($selectedDuesCleared == 'Not Cleared') {
            $query .= " AND (nd.requestId = rr.requestId AND nd.noDueApproval = 'No')";
        }
    }
}

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
if (!empty($conditions)) {
    array_unshift($conditions, $paramTypes);
    $stmt->bind_param(...$conditions);
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error executing query: " . $stmt->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($departmentName); ?> Dashboard</title>
    <link rel="stylesheet" href="../css/departmentDashboard.css">
    <style>
        .success {
            color: #08a820;
        }

        .error {
            color: #721c24;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="header-item">
                <?php if (isset($_SESSION['deptId'])): ?>
                    <div class="welcome-message">
                        <h2><?php echo htmlspecialchars($departmentName); ?> Dashboard</h2>
                    </div>
                <?php endif; ?>
            </div>
            <?php include 'deptNav.php'; ?>
        </header>

        <main>
            <?php if (isset($_SESSION['success_message'])): ?>
                <script>
                    alert("<?php echo $_SESSION['success_message']; ?>");
                </script>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <script>
                    alert("<?php echo $_SESSION['error_message']; ?>");
                </script>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>


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
                        <label for="startDate">Start Date:</label>
                        <input type="date" name="startDate" id="startDate" value="<?php echo htmlspecialchars($selectedStartDate); ?>">
                    </div>

                    <div class="filter-group">
                        <label for="endDate">End Date:</label>
                        <input type="date" name="endDate" id="endDate" value="<?php echo htmlspecialchars($selectedEndDate); ?>">
                    </div>

                    <div class="filter-group">
                        <label for="duesCleared">Dues Cleared:</label>
                        <select name="duesCleared" id="duesCleared">
                            <option value="">Select Dues Status</option>
                            <option value="Cleared" <?php if ($selectedDuesCleared == 'Cleared') echo 'selected'; ?>>Dues Cleared</option>
                            <option value="Not Cleared" <?php if ($selectedDuesCleared == 'Not Cleared') echo 'selected'; ?>>Dues Not Cleared</option>
                        </select>
                    </div>

                    <button type="submit" id="filter" name="filter">Filter</button>
                </form>
            </div>

            <h3>Details of students requesting for no dues</h3>
            <?php if ($result->num_rows > 0): ?>
                <form id="noDuesForm" action="processNoDues.php" method="post">
                    <table id="noDuesTable">
                        <thead>
                            <tr>
                                <th>Roll Number</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Request ID</th>
                                <th>Request Date</th>
                                <th>No Due Approval</th>
                                <th>No Due Comment</th>
                                <th>Approval Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['rollNo']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Course']); ?></td>
                                    <td><?php echo htmlspecialchars($row['requestId']); ?></td>
                                    <td><?php echo htmlspecialchars($row['requestDate']); ?></td>

                                    <td>
                                        <input type="hidden" name="requestId[<?php echo htmlspecialchars($row['requestId']); ?>]" value="<?php echo htmlspecialchars($row['requestId']); ?>">

                                        <?php if ($row['noDueApproval'] === 'Yes'): ?>
                                            <span>Cleared</span>
                                        <?php else: ?>
                                            <!-- <input type="radio" name="noDueApproval[<?php echo htmlspecialchars($row['requestId']); ?>]" value="Yes" required <?php echo ($row['noDueApproval'] === 'Yes') ? 'checked' : ''; ?>> Yes
                                            <input type="radio" name="noDueApproval[<?php echo htmlspecialchars($row['requestId']); ?>]" value="No" required <?php echo ($row['noDueApproval'] === 'No') ? 'checked' : ''; ?>> No -->
                                            <span>Not Cleared</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if (!empty($row['noDueComment'])): ?>
                                            <span><?php echo htmlspecialchars($row['noDueComment']); ?></span>
                                        <?php else: ?>
                                            <span>No comments yet</span>
                                        <?php endif; ?>
                                    </td>

                                    <td><?php echo htmlspecialchars($row['approvalDate']); ?></td>

                                    <td>
                                        <?php if ($row['noDueApproval'] === 'Yes'): ?>
                                            <span>Dues Cleared</span>
                                        <?php else: ?>
                                            <!-- <button type="button" class="approve-btn" onclick="submitRow('<?php echo htmlspecialchars($row['requestId']); ?>')">Submit</button> -->
                                            <span>Dues Not Cleared</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>

                    </table>
                </form>
            <?php else: ?>
                <p>No records found.</p>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function submitRow(requestId) {
            const form = document.createElement('form');
            form.method = 'post';
            form.action = 'processNoDues.php';

            const requestIdInput = document.createElement('input');
            requestIdInput.type = 'hidden';
            requestIdInput.name = `requestId[${requestId}]`;
            requestIdInput.value = requestId;
            form.appendChild(requestIdInput);

            const approvalRadios = document.querySelector(`input[name="noDueApproval[${requestId}]"]:checked`);
            if (approvalRadios) {
                const approvalInput = document.createElement('input');
                approvalInput.type = 'hidden';
                approvalInput.name = `noDueApproval[${requestId}]`;
                approvalInput.value = approvalRadios.value;
                form.appendChild(approvalInput);
            }

            const commentTextarea = document.querySelector(`textarea[name="noDueComment[${requestId}]"]`);
            if (commentTextarea) {
                const commentInput = document.createElement('input');
                commentInput.type = 'hidden';
                commentInput.name = `noDueComment[${requestId}]`;
                commentInput.value = commentTextarea.value;
                form.appendChild(commentInput);
            }
            document.body.appendChild(form);
            form.submit();
        }
    </script>


</body>

</html>