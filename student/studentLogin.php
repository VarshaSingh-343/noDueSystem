<?php
session_start();
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rollno = $_POST['rollno'] ?? '';
    $dob = $_POST['dob'] ?? '';

    $query = "SELECT * FROM student WHERE rollNo = ? AND dob = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $rollno, $dob);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['rollno'] = $rollno;
        header('Location: studentDashboard.php');
        exit();
    } else {
        $_SESSION['login_error'] = 'Invalid roll number or date of birth';
        header('Location: studentLogin.php');
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="error">
                <?php echo htmlspecialchars($_SESSION['login_error']); ?>
            </div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>
        <form action="studentLogin.php" method="post">
            <div class="item">
                <label for="rollno">Roll No:</label>
                <input type="text" name="rollno" id="rollno" required>
            </div>
            <div class="item">
                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob" id="dob" required>
            </div>
            <div class="item">
                <button type="submit">Login</button>
            </div>
        </form>
        <div class="item">
            <button class="back-button" onclick="window.location.href='../index.html'">Back to Home</button>
        </div>
    </div>
</body>
</html>
