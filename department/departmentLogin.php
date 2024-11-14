<?php
session_start();
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deptId = $_POST['deptId'];
    $deptPassword = $_POST['deptPassword'];

    // Prepare the SQL statement to prevent SQL injection
    $sql = "SELECT deptPassword FROM department WHERE deptId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $deptId);
    $stmt->execute();
    $stmt->store_result(); // Store the result to check if any rows matched

    // Check if the department ID exists
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch(); // Fetch the result to get the hashed password

        // Verify the entered password against the hashed password
        if (password_verify($deptPassword, $hashedPassword)) {
            $_SESSION['deptId'] = $deptId;

            // Redirect to department dashboard
            header("Location: departmentDashboard.php");
            exit();
        } else {
            $_SESSION['message'] = 'Invalid Department ID or Password.';
        }
    } else {
        $_SESSION['message'] = 'Invalid Department ID or Password.';
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
    <title>Department Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="container">
        <h2>Department Login</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message error">
                <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <form action="departmentLogin.php" method="POST">
            <div class="item">
                <label for="deptId">Department ID:</label>
                <input type="text" id="deptId" name="deptId" required>
            </div>
            <div class="item">
                <label for="deptPassword">Password:</label>
                <input type="password" id="deptPassword" name="deptPassword" required>
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
