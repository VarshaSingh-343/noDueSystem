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

$conditions = [];
$params = [];

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
}

$query = "SELECT refundrequest.rollNo, refundrequest.requestDate, refundrequest.refundStatus, 
                 refundrequest.refundDate, refundrequest.refundDescription
          FROM refundrequest 
          JOIN student ON refundrequest.rollNo = student.rollNo";

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $paramTypes = str_repeat("s", count($params));
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
    <link rel="stylesheet" href="adminDashboard.css">
    <style>
        
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

                <button type="submit" id="filter" name="filter">Filter</button>
            </form>
        </div>

        <div id="requestsSection">
            <h3>Details of Students Requesting for Security Money</h3>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Request Date</th>
                            <th>Refund Status</th>
                            <th>Refund Date</th>
                            <th>Refund Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['rollNo']); ?></td>
                                <td><?php echo htmlspecialchars($row['requestDate']); ?></td>
                                <td><?php echo htmlspecialchars($row['refundStatus']); ?></td>
                                <td><?php echo htmlspecialchars($row['refundDate']); ?></td>
                                <td><?php echo htmlspecialchars($row['refundDescription']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No student records found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
