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

        // Check if deptId already exists
        $checkSql = "SELECT * FROM department WHERE deptId = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("s", $deptId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If department already exists
            $_SESSION['message'] = "Department ID already exists!";
            $_SESSION['message_type'] = "error";
        } else {
            // If department doesn't exist, insert the new department
            $sql = "INSERT INTO department (deptId, deptName, deptPassword) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $deptId, $deptName, $deptPassword);
            if ($stmt->execute()) {
                $_SESSION['message'] = "New department added successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error adding department!";
                $_SESSION['message_type'] = "error";
            }
        }
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
        $_SESSION['message'] = "Department updated successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: departmentManagement.php");
        exit();
    } elseif (isset($_POST['delete'])) {
        // Deleting a department
        $deptId = $_POST['deptId'];

        $sql = "DELETE FROM department WHERE deptId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $deptId);
        $stmt->execute();
        $_SESSION['message'] = "Department deleted successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: departmentManagement.php");
        exit();
    }
}

// Fetch departments to display
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
        .container{
            justify-content: center;
            align-items: center;
        }
        table{
            width: 70%;
        }
        .message {
            width: 40%;
            text-align: center;
            padding: 10px;
            margin: 0 auto;
            border-radius: 5px;
            border: 2px solid #f03e41;
        }
        .success {
            color: #08a820;
        }
        .error {
            color: #721c24;
        }
        form, #deptForm{
            display: flex;
            flex-direction: column;
            gap: 5px;
            text-align: center;
            justify-content: left;
            align-items: center;
            flex-wrap: wrap;
        }
        .form-item{
            display: flex;
            flex-direction: row;
        }
        input[type="text"] {
            padding: 7px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 70%;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        input[type="submit"] {
            padding: 7px 12px;
            text-align: center;
            background-color: #40b858;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        #submit1:hover, #submit:hover {
            background-color: green;
            transform: scale(1.1);
        }
        label {
            font-size: 16px;
            margin: 10px 0 5px;
            color: #333;
            width: 150px;
        }
        #deptForm{
            padding: 0px 20px 20px 20px;
            margin: 0 auto;
            justify-content: center;
            align-items: center;
            text-align: center;
            width: 50%;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 4px 8px #cccccc;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .form-item1{
            display: flex;
            flex-direction: row;
            width: 80%;
        }
        .form-item1 input{
            text-align: left;
        }
        #submit{
            margin: 0 auto;
            margin-top: 15px;
            padding: 12px;
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
                                <input id="submit1" type="submit" name="edit" value="Edit">
                                <input id="submit1" type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure you want to delete this department?');">
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="deptForm">
            <form id="deptForm" action="departmentManagement.php" method="POST">
                <h2>Add New Department</h2>
                <div class="form-item1">
                    <label for="deptId">Department ID:</label>
                    <input type="text" id="deptId" name="deptId" required>
                </div>
                <div class="form-item1">
                    <label for="deptName">Department Name:</label>
                    <input type="text" id="deptName" name="deptName" required>
                </div>
                <div class="form-item1">
                    <label for="deptPassword">Department Password:</label>
                    <input type="text" id="deptPassword" name="deptPassword" required>
                </div>
                <div class="form-item1">
                    <input type="submit" id="submit" name="add" value="Add Department">
                </div>
            </form>
        </div>
        
    </div>
</body>
</html>
