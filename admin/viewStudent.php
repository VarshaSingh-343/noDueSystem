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

$query = "SELECT * FROM student";

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $paramTypes = str_repeat("s", count($params));
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$studentResult = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Data</title>
    <link rel="stylesheet" href="adminDashboard.css">
    
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

        <h2>Student Details</h2>

        <?php include 'importData.php'; ?>

        <?php if (isset($_GET['alert']) && $_GET['alert'] === 'true'): ?>
            <script>
                alert('<?php echo isset($_SESSION['message']) ? addslashes($_SESSION['message']) : ''; ?>');
                <?php unset($_SESSION['message']); ?>
            </script>
        <?php endif; ?>

        <?php if ($studentResult->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Batch Session</th>
                    <th>Enrollment No</th>
                    <th>Roll No</th>
                    <th>Course</th>
                    <th>Name</th>
                    <th>Father's Name</th>
                    <th>Mother's Name</th>
                    <th>Contact</th>
                    <th>Date of Birth</th>
                    <th>Security Amount</th>
                </tr>
                <?php while($row = $studentResult->fetch_assoc()): ?>
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
            <p>No student records found.</p>
        <?php endif; ?>

    </div>
</body>
</html>
