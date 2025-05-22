<?php
session_start();
include '../connect.php';

$selectedCourse = $_POST['course'] ?? '';
$selectedBatch = $_POST['batchSession'] ?? '';
$selectedRefundStatus = $_POST['refundStatus'] ?? '';
$selectedStartDate = $_POST['startDate'] ?? '';
$selectedEndDate = $_POST['endDate'] ?? '';
$selectedDepartmentDues = $_POST['departmentDues'] ?? '';

$conditions = [];
$params = [];
$paramTypes = '';

if ($selectedCourse) {
    $conditions[] = "s.Course = ?";
    $params[] = $selectedCourse;
    $paramTypes .= 's';
}

if ($selectedBatch) {
    $conditions[] = "s.batchSession = ?";
    $params[] = $selectedBatch;
    $paramTypes .= 's';
}

if ($selectedRefundStatus) {
    $conditions[] = "rr.refundStatus = ?";
    $params[] = $selectedRefundStatus;
    $paramTypes .= 's';
}

if ($selectedStartDate) {
    $conditions[] = "rr.requestDate >= ?";
    $params[] = $selectedStartDate;
    $paramTypes .= 's';
}

if ($selectedEndDate) {
    $conditions[] = "rr.requestDate <= ?";
    $params[] = $selectedEndDate;
    $paramTypes .= 's';
}

$query = "SELECT s.rollNo, s.name, s.course, s.batchSession, rr.requestId, s.securityAmount, 
          rr.requestDate, rr.refundStatus, rr.refundDate, rr.refundDescription, rr.verifyDetails, 
          rr.verifyReason, uc.accHolderName, uc.bankName, uc.accountNo, uc.ifscCode
          FROM refundrequest rr
          JOIN student s ON rr.rollNo = s.rollNo
          LEFT JOIN uploadcheque uc ON s.rollNo = uc.rollNo";

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY s.rollNo";

$stmt = $conn->prepare($query);

if ($params) {
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="refund_data.csv"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

$title = "No Dues Report - " . date("d-m-Y");
fputcsv($output, [$title]);

// Add an empty row for spacing
fputcsv($output, []);

fputcsv($output, ['Roll No', 'Name', 'Course', 'Batch', 'Account Holder', 'Bank Name', 'Account No', 'IFSC Code', 'Security Amount']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['rollNo'],
        $row['name'],
        $row['course'],
        $row['batchSession'],
        $row['accHolderName'],
        $row['bankName'],
        '=TEXT(' . $row['accountNo'] . ',"0")',  // Format as text
        $row['ifscCode'],
        $row['securityAmount']
    ]);
}

fclose($output);
exit();
?>
