<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['deptId'])) {
    header("Location: departmentLogin.php");
    exit();
}

$deptId = $_SESSION['deptId'];

// Prepare the SQL statement
$query = "SELECT s.rollNo, s.name, nd.requestId, nd.noDueApproval, nd.noDueComment, nd.approvalDate
          FROM nodues nd
          JOIN refundrequest rr ON nd.requestId = rr.requestId
          JOIN student s ON rr.rollNo = s.rollNo
          WHERE nd.deptId = ? AND (nd.noDueApproval != 'Yes' OR nd.noDueApproval IS NULL)";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("s", $deptId);
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
    <title>Library Department Dashboard</title>
    <link rel="stylesheet" href="departmentDashboard.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-item">
                <h2>Library Department Dashboard</h2>
            </div>
            <nav>
                <ul>
                    <li><a class="nav-link" href="LibraryDashboard.php">Dashboard</a></li>
                    <li>|</li>
                    <li><a class="nav-link" href="departmentLogout.php">Logout</a></li>
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

            <button class="toggle-btn" onclick="toggleTable()">View No Dues Requests</button>

            <?php if ($result->num_rows > 0): ?>
                <form id="noDuesForm" action="processNoDues.php" method="post">
                    <table id="noDuesTable">
                        <thead>
                            <tr>
                                <th>Roll Number</th>
                                <th>Name</th>
                                <th>Request ID</th>
                                <th>No Due Approval</th>
                                <th>No Due Comment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['rollNo']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['requestId']); ?></td>
                                    <td>
                                        <input type="hidden" name="requestId[<?php echo htmlspecialchars($row['requestId']); ?>]" value="<?php echo htmlspecialchars($row['requestId']); ?>">
                                        <input type="radio" name="noDueApproval[<?php echo htmlspecialchars($row['requestId']); ?>]" value="Yes" required> Yes
                                        <input type="radio" name="noDueApproval[<?php echo htmlspecialchars($row['requestId']); ?>]" value="No" required <?php echo ($row['noDueApproval'] === 'No') ? 'checked' : ''; ?>> No
                                    </td>
                                    <td>
                                        <textarea name="noDueComment[<?php echo htmlspecialchars($row['requestId']); ?>]" rows="2" placeholder="Add comment..."><?php echo htmlspecialchars($row['noDueComment']); ?></textarea>
                                    </td>
                                    <td>
                                        <button type="button" class="approve-btn" onclick="submitRow('<?php echo htmlspecialchars($row['requestId']); ?>')">Submit</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </form>
            <?php else: ?>
                <p>No pending no dues requests.</p>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function toggleTable() {
            const table = document.getElementById('noDuesTable');
            if (table.style.display === 'none' || table.style.display === '') {
                table.style.display = 'table';
            } else {
                table.style.display = 'none';
            }
        }

        function submitRow(requestId) {
            const form = document.createElement('form');
            form.method = 'post';
            form.action = 'processNoDues.php';

            const requestIdInput = document.createElement('input');
            requestIdInput.type = 'hidden';
            requestIdInput.name = `requestId[${requestId}]`;
            requestIdInput.value = requestId;
            form.appendChild(requestIdInput);

            const approvalRadios = document.querySelectorAll(`input[name="noDueApproval[${requestId}]"]:checked`);
            if (approvalRadios.length > 0) {
                const approvalInput = document.createElement('input');
                approvalInput.type = 'hidden';
                approvalInput.name = `noDueApproval[${requestId}]`;
                approvalInput.value = approvalRadios[0].value;
                form.appendChild(approvalInput);
            }

            const commentTextarea = document.querySelector(`textarea[name="noDueComment[${requestId}]"]`);
            if (commentTextarea) {
                const commentInput = document.createElement('textarea');
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

<?php
$stmt->close();
$conn->close();
?>
