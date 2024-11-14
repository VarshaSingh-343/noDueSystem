<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['rollno'])) {
    header("Location: studentLogin.php");
    exit();
}

$rollNo = $_SESSION['rollno'];
$sql = "SELECT * FROM student WHERE rollNo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $rollNo);
$stmt->execute();
$result = $stmt->get_result();

$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../css/studentDashboard.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-item">
                <?php if (isset($_SESSION['rollno'])): ?>
                    <div class="welcome-message">
                        Welcome to your Dashboard!
                    </div>
                <?php endif; ?>
            </div>
            <?php include 'nav.php'; ?>
        </header>
        
        <main>
            <?php if ($student): ?>
                <div class="student-detail">
                    <div><strong>Batch :</strong> <span><?php echo htmlspecialchars($student['batchSession']); ?></span></div>
                    <div><strong>Enrollment No :</strong> <span><?php echo htmlspecialchars($student['enrollmentNo']); ?></span></div>
                    <div><strong>Roll No :</strong> <span><?php echo htmlspecialchars($student['rollNo']); ?></span></div>
                    <div><strong>Name :</strong> <span><?php echo htmlspecialchars($student['Name']); ?></span></div>
                    <div><strong>Date of Birth :</strong> <span><?php echo htmlspecialchars($student['Dob']); ?></span></div>
                    <div><strong>Contact :</strong> <span><?php echo htmlspecialchars($student['Contact']); ?></span></div>
                    <div><strong>Father's Name :</strong> <span><?php echo htmlspecialchars($student['fatherName']); ?></span></div>
                    <div><strong>Mother's Name :</strong> <span><?php echo htmlspecialchars($student['motherName']); ?></span></div>
                    <div><strong>Security Amount :</strong> <span><?php echo htmlspecialchars($student['securityAmount']); ?></span></div>
                </div>
            <?php else: ?>
                <div class="student-detail">
                    <div>No details found for this roll number.</div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
