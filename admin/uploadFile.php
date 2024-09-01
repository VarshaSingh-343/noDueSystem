<?php
session_start();
include '../connect.php'; 

$maxFileSize = 2 * 1024 * 1024; // 2 MB
$allowedFileType = 'csv';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];
    $filename = $_FILES['file']['name'];
    $fileSize = $_FILES['file']['size'];
    $fileType = pathinfo($filename, PATHINFO_EXTENSION);
    $uploadDir = 'uploadFile/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if ($fileSize > $maxFileSize) {
        $_SESSION['message'] = 'File size exceeds the maximum limit of 2 MB.';
        header('Location: viewStudent.php?alert=true');
        exit();
    } elseif ($fileType != $allowedFileType) {
        $_SESSION['message'] = 'Invalid file type. Please upload a CSV file.';
        header('Location: viewStudent.php?alert=true');
        exit();
    } else {
        $uploadFile = $uploadDir . basename($filename);

        if (move_uploaded_file($file, $uploadFile)) {
            if (($handle = fopen($uploadFile, 'r')) !== FALSE) {
                $header = fgetcsv($handle); // Read header row

                if ($header === FALSE) {
                    $_SESSION['message'] = 'Failed to read the CSV header.';
                    header('Location: viewStudent.php?alert=true');
                    exit();
                }

                $stmt = $conn->prepare("INSERT INTO student (batchSession, enrollmentNo, rollNo, Course, Name, fatherName, motherName, Contact, Dob, securityAmount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $rowCount = 0; // Track number of successful rows inserted

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    // Skip empty rows
                    if (empty(array_filter($data))) {
                        continue; // Skip empty row
                    }

                    if (count($data) == 10) {
                        // Sanitize and prepare data
                        $data = array_map('trim', $data);

                        // Bind parameters and execute
                        $stmt->bind_param("sssssssssd", ...$data); // Ensure types match with schema

                        if ($stmt->execute()) {
                            $rowCount++;
                        } else {
                            error_log("Error inserting row: " . implode(", ", $data)); // Log error
                        }
                    } else {
                        error_log("Invalid row format: " . implode(", ", $data)); // Log invalid row
                    }
                }

                fclose($handle);

                if ($rowCount > 0) {
                    $_SESSION['message'] = "File successfully uploaded.";
                } else {
                    $_SESSION['message'] = 'No valid rows to insert.';
                }
                header('Location: viewStudent.php?alert=true');
                exit();
            } else {
                $_SESSION['message'] = 'Failed to open the file.';
                header('Location: viewStudent.php?alert=true');
                exit();
            }
        } else {
            $_SESSION['message'] = 'Failed to move the uploaded file.';
            header('Location: viewStudent.php?alert=true');
            exit();
        }
    }
} else {
    $_SESSION['message'] = 'No file uploaded.';
    header('Location: viewStudent.php?alert=true');
    exit();
}
?>
