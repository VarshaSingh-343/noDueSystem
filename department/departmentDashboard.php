<?php
session_start();
include '../connect.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['deptId'])) {
    header("Location: departmentLogin.php");
    exit();
}

$deptId = $_SESSION['deptId'];
$deptNames = [];
$sql = "SELECT deptId, deptName FROM department";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $deptNames[$row['deptId']] = $row['deptName'];
    }
} else {
    die("No departments found.");
}

$departmentName = isset($deptNames[$deptId]) ? $deptNames[$deptId] : 'Unknown Department';

$sql = "SELECT deptPassword FROM department WHERE deptId = '$deptId'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentPassword = $row['deptPassword'];
} else {
    $currentPassword = 'Unknown password';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["newPassword"])) {
    $newPassword = $_POST["newPassword"];
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $sql = "UPDATE department SET deptPassword = '$hashedPassword' WHERE deptId = '$deptId'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['success_message'] = "Password updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating password: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($departmentName); ?> Dashboard</title>
    <link rel="stylesheet" href="../css/departmentDashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
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

        .profile-icon {
            display: flex;
            font-size: 28px;
            align-items: center;
            justify-content: flex-end;
            margin: 10px;
            margin-left: auto;
            margin-right: 25px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            text-align: center;
            align-items: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        label {
            margin-bottom: 10px;
            font-weight: bold;
            color: #4A148C;
            display: block;
        }

        .pd {
            display: flex;
            flex-direction: row;
            align-items: center;
            font-size: 20px;
        }

        #password-toggle {
            margin-left: 10px;
            cursor: pointer;
        }

        .hide {
            display: none;
        }

        .pd,
        #update-password-form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .password-container {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 15px;
        }

        #change-password-button,
        #update-password {
            align-items: center;
            width: 200px;
            margin: 5px auto;
            background-color: #08a820;
            padding: 7px;
            border-radius: 3px;
            border: none;
        }

        #change-password-button:hover, 
        #update-password:hover{
            scale: 1.1;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="header-item">
                <?php if (isset($_SESSION['deptId'])): ?>
                    <div class="welcome-message">
                        <h2><?php echo htmlspecialchars($departmentName); ?> Dashboard</h2>
                    </div>
                <?php endif; ?>
            </div>
            <?php include 'deptNav.php'; ?>
        </header>
        <main>
            <div class="modal" id="profile-modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Department Profile</h2>
                    <div class="pd">

                        <div class="password-container">
                            <label>Current Password: </label>
                            <input type="text" id="current-password" value="*****" readonly>
                            <i class="fa fa-eye" aria-hidden="true" id="current-password-toggle" style="cursor:pointer;"></i>
                        </div>
                    </div>
                    <button id="change-password-button">Change Password</button>
                    <div id="update-password-form" class="hide">

                        <div class="password-container">
                            <label>New Password:</label>
                            <input type="password" id="new-password" placeholder="Enter new password">
                            <i class="fa fa-eye" aria-hidden="true" id="new-password-toggle" style="cursor:pointer;"></i>
                        </div>

                        <div class="password-container">
                            <label>Confirm Password:</label>
                            <input type="password" id="confirm-password" placeholder="Confirm new password">
                            <i class="fa fa-eye" aria-hidden="true" id="confirm-password-toggle" style="cursor:pointer;"></i>
                        </div>
                        <button id="update-password">Update</button>
                    </div>
                </div>
            </div>

            <div class="profile-icon">
                <i class="fa fa-user-circle" aria-hidden="true" id="profile-icon-trigger"></i>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="message"><?php echo $_SESSION['success_message'];
                                        unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error-message"><?php echo $_SESSION['error_message'];
                                            unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <a href="viewRefundRequests.php">
                <button class="toggle-btn">Approve Students No Dues Request</button>
            </a>
            <a href="approvedRequests.php">
                <button class="toggle-btn">View Requests Reports</button>
            </a>

        </main>
    </div>

    <script>
        var modal = document.getElementById("profile-modal");
var trigger = document.getElementById("profile-icon-trigger");
var closeButton = document.getElementsByClassName("close")[0];
var changePasswordButton = document.getElementById("change-password-button");
var updatePasswordForm = document.getElementById("update-password-form");
var updatePassword = document.getElementById("update-password");
var newPasswordField = document.getElementById("new-password");
var confirmPasswordField = document.getElementById("confirm-password");

// Ensure the update password form is hidden initially
updatePasswordForm.style.display = "none";

// Show modal and change password button when profile icon is clicked
trigger.addEventListener("click", function() {
    modal.style.display = "flex";
    changePasswordButton.style.display = "block";  // Show the change password button
    updatePasswordForm.style.display = "none";     // Ensure the form is hidden
});

// Close modal when close button or outside of modal is clicked
closeButton.addEventListener("click", function() {
    modal.style.display = "none";
});

window.addEventListener("click", function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

// Show the update password form when the "Change Password" button is clicked
changePasswordButton.addEventListener("click", function() {
    updatePasswordForm.style.display = "flex";   // Show the update password form
    changePasswordButton.style.display = "none";   // Hide the change password button
});

// Update password logic
updatePassword.addEventListener("click", function() {
    if (newPasswordField.value === confirmPasswordField.value && newPasswordField.value.trim() !== '') {
        if (confirm("Are you sure you want to update your password?")) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert("Password has been updated successfully!");
                    location.reload();
                }
            };
            xhr.send("newPassword=" + encodeURIComponent(newPasswordField.value));
        }
    } else {
        alert("Passwords do not match or are empty!");
    }
});

// Toggle password visibility functionality
function togglePasswordVisibility(toggleElement, passwordField) {
    toggleElement.addEventListener('click', function() {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleElement.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleElement.classList.remove('fa-eye-slash');
        }
    });
}

// Apply password visibility toggle to the respective fields
togglePasswordVisibility(document.getElementById("current-password-toggle"), document.getElementById("current-password"));
togglePasswordVisibility(document.getElementById("new-password-toggle"), newPasswordField);
togglePasswordVisibility(document.getElementById("confirm-password-toggle"), confirmPasswordField);

    </script>
</body>

</html>