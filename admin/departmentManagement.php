<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: adminLogin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $deptId = $_POST['deptId'];
        $deptName = $_POST['deptName'];
        $deptPassword = $_POST['deptPassword'];

        // Hash the department password before storing
        $hashedPassword = password_hash($deptPassword, PASSWORD_DEFAULT);

        // Check if deptId or deptName already exists
        $checkSql = "SELECT * FROM department WHERE deptId = ? OR deptName = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("ss", $deptId, $deptName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch existing row to check which field caused the conflict
            $row = $result->fetch_assoc();
            if ($row['deptId'] == $deptId) {
                $_SESSION['message'] = "Department ID already exists!";
            } elseif ($row['deptName'] == $deptName) {
                $_SESSION['message'] = "Department Name already exists!";
            }
            $_SESSION['message_type'] = "error";
        } else {
            $sql = "INSERT INTO department (deptId, deptName, deptPassword) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $deptId, $deptName, $hashedPassword);
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
    }
    // For editing an existing department
    elseif (isset($_POST['edit'])) {
        $deptId = $_POST['deptId'];
        $deptName = $_POST['deptName'];
        $deptPassword = $_POST['deptPassword'];
        $currentPasswordHash = $_POST['currentPasswordHash']; // Get current password hash from the hidden field

        if (empty($deptPassword)) {
            // If no new password is provided, just update the department name
            $sql = "UPDATE department SET deptName = ? WHERE deptId = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $deptName, $deptId);
        } else {
            // If a new password is provided, hash it and update the password
            $hashedPassword = password_hash($deptPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE department SET deptName = ?, deptPassword = ? WHERE deptId = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $deptName, $hashedPassword, $deptId);
        }

        // Execute the query and handle the response
        if ($stmt->execute()) {
            $_SESSION['message'] = "Department updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating department!";
            $_SESSION['message_type'] = "error";
        }

        header("Location: departmentManagement.php");
        exit();
    }

    // For deleting a department
    elseif (isset($_POST['delete'])) {
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

$sql = "SELECT deptId, deptName, deptPassword FROM department";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Management</title>
    <link rel="stylesheet" href="../css/adminDashboard.css">
    <link rel="stylesheet" href="../css/noduesproject.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        .container {
            justify-content: center;
            align-items: center;
        }

        table {
            width: 90%;
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

        form,
        #deptForm {
            display: flex;
            flex-direction: column;
            gap: 5px;
            text-align: center;
            justify-content: left;
            align-items: center;
            flex-wrap: wrap;
        }

        .form-item {
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

        .submit1,
        #submit {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            background-color: #40b858;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 16px;
            margin-right: 10px;
        }

        .submit1 i {
            margin-right: 5px;
        }

        .submit1:hover,
        #submit:hover {
            background-color: green;
            transform: scale(1.1);
        }

        label {
            font-size: 16px;
            margin: 10px 0 5px;
            color: #333;
            width: 150px;
        }

        .deptForm{
            margin-top: 35px;
        }

        #deptForm {
            padding: 0px 20px 20px 20px;
            margin: 0 auto;
            justify-content: center;
            align-items: center;
            text-align: center;
            width: 60%;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 4px 8px #cccccc;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 15px;
        }

        .form-item1 {
            display: flex;
            flex-direction: row;
            width: 80%;
        }

        .form-item1 input {
            text-align: left;
        }

        #submit {
            margin: 0 auto;
            margin-top: 15px;
            padding: 12px;
        }

        #pass {
            width: 200px;
        }
    </style>
    <script>
        function confirmEdit() {
            return confirm("Are you sure you want to update this department?");
        }

        function confirmAdd() {
            return confirm("Are you sure you want to add this new department?");
        }
    </script>
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
                            <td><input type="text" name="deptId" value="<?php echo htmlspecialchars($row['deptId']); ?>" readonly></td>
                            <td><input type="text" name="deptName" value="<?php echo htmlspecialchars($row['deptName']); ?>" required></td>
                            <td>
                                <textarea id="pass" name="deptPassword" placeholder="Enter new password (leave blank to keep current)"></textarea>
                                <span>********</span>
                            </td>
                            <td>
                                <input type="hidden" name="currentPasswordHash" value="<?php echo htmlspecialchars($row['deptPassword']); ?>">

                                <button class="submit1" name="edit" onclick="return confirmEdit();">
                                    <i class="fa fa-pencil" aria-hidden="true"></i> Update
                                </button>

                                <button class="submit1" name="delete" onclick="return confirm('Are you sure you want to delete this department?');">
                                    <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                </button>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>

        </table>

        <div class="deptForm">
            <form id="deptForm" action="departmentManagement.php" method="POST" onsubmit="return confirmAdd();">
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

    <script>
        <?php if (isset($_SESSION['message'])): ?>
            alert('<?php echo $_SESSION['message']; ?>');
            <?php unset($_SESSION['message']);
            unset($_SESSION['message_type']); ?>
        <?php endif; ?>
    </script>

</body>

</html>