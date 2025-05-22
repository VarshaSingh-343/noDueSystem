<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $plainPassword = $_POST['password']; // Get the password from the form
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT); // Hash the password
    echo "Plain Password: " . htmlspecialchars($plainPassword) . "<br>";
    echo "Hashed Password: " . htmlspecialchars($hashedPassword) . "<br>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hash Password</title>
</head>
<body>
    <h2>Password Hashing Tool</h2>
    <form method="POST">
        <label for="password">Enter Password:</label>
        <input type="text" id="password" name="password" required>
        <button type="submit">Hash Password</button>
    </form>
</body>
</html>
