<?php
session_start();
include '../connect.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: adminLogin.php");
    exit();
}

// Handle form submissions for adding a new department
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Adding a new department
        $deptId = $_POST['deptId'];
        $deptName = $_POST['deptName'];
        $deptPassword = $_POST['deptPassword'];

        $sql = "INSERT INTO department (deptId, deptName, deptPassword) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $deptId, $deptName, $deptPassword);
        $stmt->execute();
        header("Location: departmentManagement.php");
        exit();
    } elseif (isset($_POST['edit'])) {
        // Editing an existing department
        $deptId = $_POST['deptId'];
        $deptName = $_POST['deptName'];
        $deptPassword = $_POST['deptPassword'];

        $sql = "UPDATE department SET deptName = ?, deptPassword = ? WHERE deptId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $deptName, $deptPassword, $deptId);
        $stmt->execute();
        header("Location: departmentManagement.php");
        exit();
    } elseif (isset($_POST['delete'])) {
        // Deleting a department
        $deptId = $_POST['deptId'];

        $sql = "DELETE FROM department WHERE deptId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $deptId);
        $stmt->execute();
        header("Location: departmentManagement.php");
        exit();
    }
}

$sql = "SELECT deptId, deptName, deptPassword FROM department";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Management</title>
    <link rel="stylesheet" href="adminDashboard.css">
    <style>
        form {
        display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    text-align: center;
    border: 2px solid #333;
    border-radius: 10px;
    padding: 20px;
    width: 40%;
    background-color: #f9f9f9;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    gap: 10px;
}
.form-item{
    display: flex;
    flex-direction: row;
}
input[type="text"] {
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 80%;
    box-sizing: border-box;
    transition: border-color 0.3s;
}

input[type="text"]:focus {
    border-color: #4CAF50;
    outline: none;
}

input[type="submit"] {
    padding: 12px 20px;
    margin: 10px 0;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.3s;
}

input[type="submit"]:hover {
    background-color: #45a049;
    transform: scale(1.05);
}

label {
    font-size: 16px;
    margin: 10px 0 5px;
    color: #333;
    width: 150px;
}

h2 {
    margin-bottom: 20px;
    color: #333;
}

    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-item">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="welcome-message">
                        <h2>Department details</h2>
                    </div>
                <?php endif; ?>
            </div>
            <?php include 'adminNav.php'; ?>
        </header>
        <h2>Department Management</h2>

        <!-- Display Departments -->
        <table>
            <thead>
                <tr>
                    <th>Department ID</th>
                    <th>Department Name</th>
                    <th>Department Password</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <form action="departmentManagement.php" method="POST">
                            <td><input type="text" name="deptId" value="<?php echo $row['deptId']; ?>" readonly></td>
                            <td><input type="text" name="deptName" value="<?php echo $row['deptName']; ?>" required></td>
                            <td><input type="text" name="deptPassword" value="<?php echo $row['deptPassword']; ?>" required></td>
                            <td>
                                <input type="submit" name="edit" value="Edit">
                                <input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure you want to delete this department?');">
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Add New Department Form -->
        <h2>Add New Department</h2>
        <form action="departmentManagement.php" method="POST">
            <div class="form-item">
            <label for="deptId">Department ID:</label>
            <input type="text" id="deptId" name="deptId" required><br>
            </div>
            
            <div class="form-item">
            <label for="deptName">Department Name:</label>
            <input type="text" id="deptName" name="deptName" required><br>
            </div>
            
            <div class="form-item">
            <label for="deptPassword">Department Password:</label>
            <input type="text" id="deptPassword" name="deptPassword" required><br>
            </div>
            
            <div class="form-item">
            <input type="submit" name="add" value="Add Department">
            </div>
            
        </form>

    </div>
</body>
</html>
