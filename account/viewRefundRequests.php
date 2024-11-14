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
    if (!empty($_POST['verifyDetails'])) {
        $selectedVerifyDetails = $_POST['verifyDetails'];
        if ($selectedVerifyDetails == 'Verified') {
            $conditions[] = "rr.verifyDetails = ?";
            $params[] = $selectedVerifyDetails;
        } else if ($selectedVerifyDetails == 'Not Verified') {
            $conditions[] = "rr.verifyDetails = 'Not Verified' OR rr.verifyDetails = 'selected' OR rr.verifyDetails = '' ";
        }
    }
}
$query = "SELECT s.rollNo, s.name, s.course, s.batchSession, rr.requestId, s.securityAmount, rr.requestDate, rr.refundDate, rr.refundDescription, rr.verifyDetails, rr.verifyReason,
          (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId AND n.noDueApproval = 'Yes') as countYes,
          (SELECT COUNT(*) FROM nodues n WHERE n.requestId = rr.requestId) as totalDepts,
          uc.filePath, uc.accHolderName, uc.bankName, uc.accountNo, uc.ifscCode
          FROM refundrequest rr
          JOIN student s ON rr.rollNo = s.rollNo
          LEFT JOIN uploadcheque uc ON s.rollNo = uc.rollNo where rr.refundStatus != 'Yes'";
if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}
$query .= " ORDER BY s.rollNo";
// Prepare the query
$stmt = $conn->prepare($query);
// Check if the prepare failed
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}
if (!empty($params)) {
    $paramTypes = str_repeat("s", count($params));
    $stmt->bind_param($paramTypes, ...$params);
}
// Execute the query
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
                        <h2>Students No Dues Requests</h2>
                    </div>
                <?php endif; ?>
            </div>
            <?php include 'accountNav.php'; ?>
        </header>
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
                    <label for="departmentDues">Filter by Department Dues:</label>
                    <select name="departmentDues" id="departmentDues">
                        <option value="">Select Dues Status</option>
                        <option value="Cleared" <?php if ($selectedDepartmentDues == 'Cleared') echo 'selected'; ?>>Dues Cleared</option>
                        <option value="Not Cleared" <?php if ($selectedDepartmentDues == 'Not Cleared') echo 'selected'; ?>>Dues Not Cleared</option>
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
                    <label for="verifyDetails">Filter by Verify Details:</label>
                    <select name="verifyDetails" id="verifyDetails">
                        <option value="">Select Verify Details</option>
                        <option value="Verified">Verified</option>
                        <option value="Not Verified">Not Verified</option>
                    </select>
                </div>
                <button type="submit" id="filter" name="filter">Filter</button>
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
                            <th>Verify Details</th>
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
                                    <a href="#" onclick="openNoDuesModal('<?php echo $row['requestId']; ?>')">
                                        <?php echo $row['countYes'] == $row['totalDepts'] ? 'Cleared' : 'Not Cleared'; ?>
                                    </a>
                                </td>

                                </td>
                                <td>
                                    <?php if ($row['verifyDetails'] == 'Verified'): ?>
                                        <span>Verified</span>
                                    <?php else: ?>
                                        <form method="POST" action="processRefund.php">
                                            <input type="hidden" name="requestId" value="<?php echo $row['requestId']; ?>">
                                            <select name="verifyDetails" class="verifyDetails" onchange="toggleVerifyReason(this)">
                                                <option value="selected">Select option</option>
                                                <option value="Verified" <?php if ($row['verifyDetails'] == 'Verified') echo 'selected'; ?>>Verified</option>
                                                <option value="Not Verified" <?php if ($row['verifyDetails'] == 'Not Verified' || $row['verifyDetails'] === NULL) echo 'selected'; ?>>Not Verified</option>
                                            </select>
                                            <?php if ($row['verifyDetails'] == 'Not Verified' && $row['verifyReason'] !== ''): ?>
                                                <textarea name="verifyReason" class="verifyReason" id="verifyReason-<?php echo $row['requestId']; ?>" style="display: block;"><?php echo htmlspecialchars($row['verifyReason']); ?></textarea>
                                            <?php elseif ($row['verifyDetails'] !== 'Verified' && $row['verifyReason'] !== ''): ?>
                                                <textarea name="verifyReason" class="verifyReason" id="verifyReason-<?php echo $row['requestId']; ?>" style="display: block;"><?php echo htmlspecialchars($row['verifyReason']); ?></textarea>
                                            <?php else: ?>
                                                <textarea name="verifyReason" class="verifyReason" id="verifyReason-<?php echo $row['requestId']; ?>" style="display: none;"><?php echo htmlspecialchars($row['verifyReason']); ?></textarea>
                                            <?php endif; ?>
                                            <button type="submit" class="greenYes" name="submitVerify" onclick="submitVerifyForm(this)">Submit</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['verifyDetails'] === 'Verified'): ?>
                                        <?php if ($row['countYes'] == $row['totalDepts']): ?>
                                            <button class="greenYes" onclick="openModal('<?php echo $row['requestId']; ?>', '<?php echo $row['filePath']; ?>')">Initiate Refund</button>
                                        <?php else: ?>
                                            <button id="redNo">No Refund</button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if ($row['verifyDetails'] == 'selected' || (isset($_POST['verifyDetails']) && $_POST['verifyDetails'] == 'selected')): ?>
                                            <button id="notVerified">No Action</button>
                                        <?php else: ?>
                                            <button id="notVerified"><?php echo $row['verifyDetails'] === 'Not Verified' || $row['verifyDetails'] === '' ? 'Account Not Verified' : ''; ?></button>
                                        <?php endif; ?>
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

    <div id="refundModal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Initiate Refund</h2>
            <form id="refundForm" method="POST" action="processRefund.php">
                <input type="hidden" name="requestId" id="requestId">
                <div class="form-group">
                    <label for="filePath">Uploaded Cheque:</label>
                    <a id="checkImage" href="#" target="_blank">View Uploaded Cheque</a>
                </div>
                <div class="form-group">
                    <label for="refundDescription">Refund Description:</label>
                    <textarea name="refundDescription" id="refundDescription" required></textarea>
                </div>
                <button id="submitModal" type="submit">Submit</button>
            </form>
        </div>
    </div>


    <!-- No Dues Modal -->
    <div id="noDuesModal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeNoDuesModal()">&times;</span>
            <h2>No Dues Details</h2>
            <div id="noDuesDetails">
                <!-- No Dues Table will be populated here -->
            </div>
        </div>
    </div>


    <script>
        function openModal(requestId, filePath) {
            document.getElementById('requestId').value = requestId;
            var checkImageLink = document.getElementById('checkImage');
            checkImageLink.href = filePath;
            checkImageLink.innerText = "View Uploaded Cheque";
            document.getElementById('refundModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('refundModal').style.display = 'none';
        }

        function toggleVerifyReason(select) {
            var textarea = document.getElementById('verifyReason-' + select.form.requestId.value);
            if (select.value === 'Not Verified') {
                textarea.style.display = 'block';
            } else {
                textarea.style.display = 'none';
            }
        }

        function submitVerifyForm(button) {
            var form = button.form;
            form.submit();
        }


        function openNoDuesModal(requestId) {
            // Send an AJAX request to fetch the department-wise dues for the given requestId
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "fetchNoDuesDetails.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Parse the response and populate the modal content
                    document.getElementById('noDuesDetails').innerHTML = xhr.responseText;
                    // Show the modal
                    document.getElementById('noDuesModal').style.display = 'block';
                }
            };
            xhr.send("requestId=" + requestId);
        } 

        function closeNoDuesModal() {
            document.getElementById('noDuesModal').style.display = 'none';
        }
    </script>

</body>

</html>