<?php
session_start();
$validUsername = 'account';
$validPassword = '54321';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === $validUsername && $password === $validPassword) {
        $_SESSION['username'] = $username;
        header('Location: accountDashboard.php');
        exit();
    } else {
        $_SESSION['login_error'] = 'Invalid username or password';
        header('Location: accountLogin.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Login</title>
    <link rel="stylesheet" href="../login.css">
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
        <form action="accountLogin.php" method="post">
            <div class="item">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="item">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
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
